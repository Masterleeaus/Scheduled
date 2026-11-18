<?php
declare(strict_types=1);

/* Unit test: per-driver claim SQL and the connection guard. No database server needed. Run: php tests/booking_pg_ledger_dialect_test.php */

require __DIR__ . '/../core/app/Booking/Public/Redemption/RedeemLedger.php';
require __DIR__ . '/../core/app/Booking/Public/Redemption/CommitConnection.php';

use App\Booking\Public\Redemption\CommitConnection;
use App\Booking\Public\Redemption\RedeemLedger;

$failures = 0;
$check = static function (string $name, bool $ok) use (&$failures): void {
    if ($ok) {
        echo "PASS $name\n";
        return;
    }
    $failures++;
    echo "FAIL $name\n";
};
$throws = static function (callable $fn, string $needle): bool {
    try {
        $fn();
    } catch (\Throwable $e) {
        return str_contains($e->getMessage(), $needle);
    }
    return false;
};

// MySQL keeps the original statement byte for byte.
$check('mysql claim SQL is unchanged INSERT IGNORE', RedeemLedger::claimSql('mysql') === 'INSERT IGNORE INTO titan_booking_intent_claims (claim_key, owner_id, fingerprint, state) VALUES (?, ?, ?, ?)');

// PostgreSQL uses ON CONFLICT on the primary key and has no INSERT IGNORE.
$pg = RedeemLedger::claimSql('pgsql');
$check('pgsql claim SQL uses ON CONFLICT (claim_key) DO NOTHING', str_contains($pg, 'ON CONFLICT (claim_key) DO NOTHING'));
$check('pgsql claim SQL has no INSERT IGNORE', !str_contains($pg, 'INSERT IGNORE'));
$check('pgsql claim SQL keeps placeholders and columns', str_contains($pg, 'INSERT INTO titan_booking_intent_claims (claim_key, owner_id, fingerprint, state) VALUES (?, ?, ?, ?)'));

// Other drivers fail closed.
$check('unknown driver is refused', $throws(static fn () => RedeemLedger::claimSql('sqlite'), 'requires MySQL or PostgreSQL'));

// CommitConnection accepts MySQL and PostgreSQL only.
$sqlite = new \PDO('sqlite::memory:', null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
$check('CommitConnection refuses sqlite', $throws(static fn () => CommitConnection::assert($sqlite, 'titan_booking_test_x'), 'requires MySQL or PostgreSQL'));

echo $failures === 0 ? "PASS booking_pg_ledger_dialect_test\n" : "FAILED $failures\n";
exit($failures === 0 ? 0 : 1);
