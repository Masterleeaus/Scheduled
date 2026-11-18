<?php
declare(strict_types=1);

/*
 * Live PostgreSQL test for the booking claim ledger (#53). Needs a disposable database.
 * Env: TEST_PGSQL_DSN (for example pgsql:host=127.0.0.1;port=5432;dbname=titan_booking_test_pg),
 *      TEST_PGSQL_USER, TEST_PGSQL_PASSWORD. The database name must start with titan_booking_test_.
 * The claims table is a session TEMP table, so nothing persists in the database.
 */

require __DIR__ . '/../../core/app/Booking/Public/Redemption/RedeemLedger.php';
require __DIR__ . '/../../core/app/Booking/Public/Redemption/CommitConnection.php';

use App\Booking\Public\Redemption\CommitConnection;
use App\Booking\Public\Redemption\RedeemLedger;

$dsn = getenv('TEST_PGSQL_DSN') ?: '';
if (!extension_loaded('pdo_pgsql') || !in_array('pgsql', PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "FAIL: pdo_pgsql is required for the PostgreSQL ledger test\n");
    exit(1);
}
if (!preg_match('/^pgsql:(?:.*;)?dbname=(titan_booking_test_[a-zA-Z0-9_]+)(?:;|$)/D', $dsn, $m)) {
    fwrite(STDERR, "FAIL: TEST_PGSQL_DSN must name a disposable titan_booking_test_* database\n");
    exit(1);
}
$dbName = $m[1];

$pdo = new PDO($dsn, getenv('TEST_PGSQL_USER') ?: '', getenv('TEST_PGSQL_PASSWORD') ?: '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

$failures = 0;
$check = static function (string $name, bool $ok) use (&$failures): void {
    echo ($ok ? 'PASS ' : 'FAIL ') . $name . "\n";
    if (!$ok) {
        $failures++;
    }
};

$pdo->exec(
    'CREATE TEMP TABLE titan_booking_intent_claims (
        claim_key CHAR(64) PRIMARY KEY,
        owner_id BIGINT NOT NULL,
        fingerprint CHAR(64) NOT NULL,
        state VARCHAR(16) NOT NULL,
        booking_id BIGINT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )'
);

$key = str_repeat('a', 64);
$fp = str_repeat('b', 64);
$ledger = new RedeemLedger($pdo);

$check('connection guard accepts pgsql on the named database', (function () use ($pdo, $dbName): bool {
    CommitConnection::assert($pdo, $dbName);
    return true;
})());
$check('connection guard rejects a different database name', (function () use ($pdo): bool {
    try {
        CommitConnection::assert($pdo, 'titan_booking_test_not_this_one');
    } catch (\RuntimeException $e) {
        return str_contains($e->getMessage(), 'Tenant database mismatch');
    }
    return false;
})());

$check('first claim inserts a row', $ledger->claim($key, 7, $fp) === true);
$check('repeat claim with the same key is a no-op, not an error', $ledger->claim($key, 7, $fp) === false);
$row = $ledger->read($key);
$check('read returns the claimed row', is_array($row) && $row['state'] === 'claimed' && $row['owner_id'] == 7);
$check('commit moves the claim to committed', $ledger->commit($key, 7, $fp, 99) === true);
$check('second commit on a committed claim changes nothing', $ledger->commit($key, 7, $fp, 99) === false);
$row = $ledger->read($key);
$check('committed row keeps its booking id', is_array($row) && $row['state'] === 'committed' && $row['booking_id'] == 99);

echo $failures === 0 ? "PASS ledger_live_test\n" : "FAILED $failures\n";
exit($failures === 0 ? 0 : 1);
