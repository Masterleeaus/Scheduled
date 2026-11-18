<?php
declare(strict_types=1);

/*
 * PostgreSQL acceptance test for the booking redemption claim ledger (Scheduled #15).
 *
 * Needs a real PostgreSQL server. Set TEST_PG_HOST, TEST_PG_PORT, TEST_PG_DB, TEST_PG_USER and TEST_PG_PASSWORD.
 * The test creates its own random schema and drops it at the end, so it does not touch other tables.
 * Missing settings are a failure, not a skip.
 *
 * Run: php integration/tests/pass_redeem_pgsql.php
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\Booking\\Public\\Redemption\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = dirname(__DIR__, 2).'/core/app/Booking/Public/Redemption/'.substr($class, strlen($prefix)).'.php';
    if (is_file($file)) {
        require $file;
    }
});

use App\Booking\Public\Redemption\CommitConnection;
use App\Booking\Public\Redemption\RedeemLedger;
use App\Booking\Public\Redemption\RedeemSchema;

if (($argv[1] ?? '') === '--racer') {
    // Child mode: one claim attempt on its own connection. Exit 0 = won, 2 = lost, 3 = error.
    [, , $raceSchema, $raceKey, $raceFingerprint] = $argv + [null, null, null, null, null];
    try {
        $racer = new PDO(
            sprintf('pgsql:host=%s;port=%s;dbname=%s', getenv('TEST_PG_HOST'), getenv('TEST_PG_PORT'), getenv('TEST_PG_DB')),
            getenv('TEST_PG_USER'),
            getenv('TEST_PG_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
        );
        $racer->exec('SET search_path TO '.$raceSchema);
        exit((new RedeemLedger($racer))->claim($raceKey, 3, $raceFingerprint) ? 0 : 2);
    } catch (Throwable) {
        exit(3);
    }
}

$env = [];
foreach (['TEST_PG_HOST', 'TEST_PG_PORT', 'TEST_PG_DB', 'TEST_PG_USER', 'TEST_PG_PASSWORD'] as $name) {
    $value = getenv($name);
    if ($value === false || $value === '') {
        fwrite(STDERR, "FAIL: {$name} is not set\n");
        exit(1);
    }
    $env[$name] = $value;
}

$dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $env['TEST_PG_HOST'], $env['TEST_PG_PORT'], $env['TEST_PG_DB']);
$connect = static function () use ($dsn, $env): PDO {
    $pdo = new PDO($dsn, $env['TEST_PG_USER'], $env['TEST_PG_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
};

$schema = 'redeem_test_'.bin2hex(random_bytes(6));
$failures = [];
$passed = 0;
function check(bool $condition, string $label, array &$failures, int &$passed): void
{
    if ($condition) {
        $passed++;
    } else {
        $failures[] = $label;
    }
}

$key = str_repeat('a', 64);
$otherKey = str_repeat('b', 64);
$fingerprint = str_repeat('c', 64);

$db = $connect();
$db->exec("CREATE SCHEMA {$schema}");
$db->exec("SET search_path TO {$schema}");
try {
    $db->exec(RedeemSchema::ddl());
    $guardAccepts = true;
    try {
        CommitConnection::assert($db, $env['TEST_PG_DB']);
    } catch (Throwable) {
        $guardAccepts = false;
    }
    check($guardAccepts, 'commit connection guard accepts pgsql', $failures, $passed);
    $guardRefusesOther = false;
    try {
        CommitConnection::assert($db, 'some_other_database');
    } catch (RuntimeException) {
        $guardRefusesOther = true;
    }
    check($guardRefusesOther, 'commit connection guard refuses a database mismatch', $failures, $passed);

    $ledger = new RedeemLedger($db);

    check($ledger->claim($key, 7, $fingerprint) === true, 'first claim wins', $failures, $passed);
    check($ledger->claim($key, 7, $fingerprint) === false, 'duplicate claim loses without error', $failures, $passed);
    check($ledger->claim($key, 9, $fingerprint) === false, 'duplicate claim from another owner loses', $failures, $passed);

    $row = $ledger->read($key);
    check(is_array($row) && (string) $row['owner_id'] === '7' && $row['state'] === 'claimed', 'read returns the first claim', $failures, $passed);

    check($ledger->commit($key, 7, $fingerprint, 42) === true, 'commit by owner succeeds', $failures, $passed);
    check($ledger->commit($key, 7, $fingerprint, 43) === false, 'second commit is refused', $failures, $passed);
    $row = $ledger->read($key);
    check(is_array($row) && $row['state'] === 'committed' && (string) $row['booking_id'] === '42', 'committed row keeps booking id', $failures, $passed);

    $threw = false;
    try {
        $ledger->claim($otherKey, 0, $fingerprint);
    } catch (InvalidArgumentException) {
        $threw = true;
    }
    check($threw, 'owner id 0 is refused before SQL', $failures, $passed);

    $threw = false;
    try {
        $db->exec("INSERT INTO titan_booking_intent_claims (claim_key, owner_id, fingerprint, state) VALUES ('{$otherKey}', 1, '{$fingerprint}', 'bogus')");
    } catch (PDOException) {
        $threw = true;
    }
    check($threw, 'state CHECK rejects unknown state', $failures, $passed);

    // Concurrency: several separate PHP processes race for the same key. Exactly one may win.
    // Separate processes, not pcntl_fork: a forked child shares the parent's PDO socket and would close it on exit.
    $raceKey = str_repeat('d', 64);
    $racers = 8;
    $procs = [];
    for ($i = 0; $i < $racers; $i++) {
        $proc = proc_open(
            [PHP_BINARY, __FILE__, '--racer', $schema, $raceKey, $fingerprint],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        if (!is_resource($proc)) {
            $failures[] = 'could not start racer process';
            break;
        }
        $procs[] = $proc;
    }
    $wins = 0;
    $losses = 0;
    $errors = 0;
    foreach ($procs as $proc) {
        $code = proc_close($proc);
        if ($code === 0) {
            $wins++;
        } elseif ($code === 2) {
            $losses++;
        } else {
            $errors++;
        }
    }
    check($wins === 1 && $losses === $racers - 1 && $errors === 0, "exactly one racing claim wins (wins={$wins}, losses={$losses}, errors={$errors})", $failures, $passed);
} finally {
    $db->exec("DROP SCHEMA IF EXISTS {$schema} CASCADE");
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL: {$failure}\n");
    }
    exit(1);
}

echo "PASS pass_redeem_pgsql ({$passed} checks)\n";
