<?php
declare(strict_types=1);

/*
 * S4: booking_create contract (integration/contracts/pass05_booking_lifecycle.json).
 * No database, no network. Exercises the pure validation, flag and idempotency layers.
 */

require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/../../core/app/Booking/BookingOwnerRequest.php';

use App\Booking\BookingOwnerRequest;
use Titan\BookingSidecar\BookingCommand;
use Titan\BookingSidecar\BookingIdempotency;
use Titan\BookingSidecar\BookingKey;
use Titan\BookingSidecar\BookingOwnerOperations;
use Titan\BookingSidecar\BookingSubmissionSafety;
use Titan\BookingSidecar\BookingWriteFeatureGate;

$checks = 0;
function s4_check(bool $ok, string $label): void
{
    global $checks;
    if (!$ok) {
        throw new RuntimeException('FAIL: '.$label);
    }
    $checks++;
}

function s4_throws(callable $fn, string $class): bool
{
    try {
        $fn();
    } catch (Throwable $e) {
        return $e instanceof $class;
    }
    return false;
}

$contract = json_decode((string) file_get_contents(__DIR__.'/../contracts/pass05_booking_lifecycle.json'), true, 32, JSON_THROW_ON_ERROR);
$required = $contract['booking_create_required'];
s4_check($required === ['idempotency_key', 'staff_ids', 'service', 'customer', 'start_at', 'timezone', 'duration_minutes'], 'contract required field list');
s4_check($contract['status'] === 'implemented_default_off', 'contract status reflects default-off implementation');

$validKey = str_repeat('k', 24);
$valid = [
    'idempotency_key' => $validKey,
    'staff_ids' => [7, 3, 7],
    'service' => ['service_id' => 15, 'method_id' => 2, 'method_unit_id' => 4, 'quantity' => 1],
    'customer' => ['name' => 'Test Customer', 'email' => 'customer@example.test', 'phone' => '+61 400 000 000'],
    'start_at' => '2031-03-04 09:00:00',
    'timezone' => 'Australia/Melbourne',
    'duration_minutes' => 90,
];

// Required fields: each one must be rejected when missing.
foreach ($required as $field) {
    $input = $valid;
    unset($input[$field]);
    s4_check(s4_throws(fn () => BookingOwnerRequest::create($input), InvalidArgumentException::class), "missing $field rejected");
}

// Valid payload is accepted and normalised (staff de-duplicated, duration as int).
$parsed = BookingOwnerRequest::create($valid);
s4_check($parsed['staff_ids'] === [7, 3], 'staff ids de-duplicated');
s4_check($parsed['duration_minutes'] === 90, 'duration normalised');

// Sidecar command: authoritative window, no price field survives parsing.
$cmd = BookingCommand::create($valid);
s4_check(!array_key_exists('price', $cmd) && !array_key_exists('total', $cmd), 'command carries no client price');
s4_check($cmd['window']['end'] - $cmd['window']['start'] === 90 * 60, 'window length equals duration');
s4_check(s4_throws(fn () => BookingCommand::create(array_merge($valid, ['idempotency_key' => 'short'])), InvalidArgumentException::class), 'short idempotency key rejected');

// Client-supplied price and privileged fields are never accepted.
$withPrice = $valid;
$withPrice['service']['price'] = '0.01';
$withPrice['service']['total_cents'] = 1;
$cmdPrice = BookingCommand::create($withPrice);
s4_check(!array_key_exists('price', $cmdPrice['service']) && !array_key_exists('total_cents', $cmdPrice['service']), 'nested client price ignored');
foreach (['price', 'total', 'payment_status', 'tenant_id', 'booking_writes_enabled'] as $privileged) {
    s4_check(s4_throws(fn () => BookingSubmissionSafety::assertSafe([$privileged => 1]), InvalidArgumentException::class), "privileged field $privileged rejected");
}

// Flags: the write gate is closed by default and needs every verified flag.
s4_check(BookingWriteFeatureGate::enabled([]) === false, 'write gate closed with empty config');
s4_check(BookingWriteFeatureGate::enabled([
    'booking_writes_enabled' => true,
    'booking_schema_verified' => true,
    'booking_rate_mapping_verified' => true,
]) === false, 'write gate closed without concurrency verification');
s4_check(BookingWriteFeatureGate::enabled([
    'booking_writes_enabled' => true,
    'booking_schema_verified' => true,
    'booking_rate_mapping_verified' => true,
    'booking_concurrency_verified' => true,
]) === true, 'write gate opens only with all verified flags');

// Owner gate: no database is touched because these checks fail before any connection use.
$pdo = (new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();
$ops = new BookingOwnerOperations($pdo, ['owner_operations_enabled' => false, 'authenticated_owner_id' => 7]);
s4_check(s4_throws(fn () => $ops->dispatch('owner_booking_create', $valid), DomainException::class), 'owner operations disabled blocks create');
$noOwner = new BookingOwnerOperations($pdo, ['owner_operations_enabled' => true]);
s4_check(s4_throws(fn () => $noOwner->dispatch('owner_booking_create', $valid), DomainException::class), 'create without authenticated owner rejected');
$ownerNoWrites = new BookingOwnerOperations($pdo, ['owner_operations_enabled' => true, 'authenticated_owner_id' => 7]);
s4_check(s4_throws(fn () => $ownerNoWrites->dispatch('owner_booking_create', $valid), RuntimeException::class), 'create with writes disabled is refused');

// Durable idempotency decision layer.
$fp = hash('sha256', 'command-a');
s4_check(BookingIdempotency::decide(null, $fp) === 'new', 'idempotency: first claim is new');
s4_check(BookingIdempotency::decide(['fingerprint' => $fp, 'state' => 'pending', 'booking_id' => null], $fp) === 'pending', 'idempotency: pending row blocks');
s4_check(BookingIdempotency::decide(['fingerprint' => $fp, 'state' => 'completed', 'booking_id' => 42], $fp) === 'replay', 'idempotency: completed row replays');
s4_check(s4_throws(fn () => BookingIdempotency::decide(['fingerprint' => $fp, 'state' => 'completed', 'booking_id' => 42], hash('sha256', 'command-b')), DomainException::class), 'idempotency: key reuse with different command rejected');
s4_check(BookingKey::fingerprint($cmd) === BookingKey::fingerprint(BookingCommand::create($valid)), 'fingerprint is deterministic');

// Tenant ledger migration must carry the columns BookingRequestLedger uses.
$ledgerSrc = (string) file_get_contents(__DIR__.'/../../private/booking-sidecar/BookingRequestLedger.php');
$migration = (string) file_get_contents(__DIR__.'/../../private/booking-sidecar/migrations/0001_titan_booking_requests.sql');
foreach (['request_key', 'fingerprint', 'state', 'booking_id'] as $column) {
    s4_check(str_contains($ledgerSrc, $column) && preg_match('/^\s*'.$column.'\s/m', $migration) === 1, "ledger column $column in migration");
}
s4_check(!preg_match('/\b(DROP|ALTER|DELETE|TRUNCATE)\b/i', $migration), 'migration is create-only');

// Laravel flag: owner_booking_create is off by default.
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $default;
    }
}
$internal = require __DIR__.'/../../core/config/booking_internal.php';
s4_check(($internal['owner_booking_create_enabled'] ?? null) === false, 'Laravel owner_booking_create flag defaults to false');

echo "PASS s4_booking_create_contract ($checks checks)\n";
