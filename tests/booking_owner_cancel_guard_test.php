<?php
declare(strict_types=1);

/*
 * owner_booking_cancel submission guard. Included from tests/run.php, which defines check().
 * No network and no database: the PDO double records any database touch and throws.
 */

require_once __DIR__.'/../private/booking-sidecar/bootstrap.php';

use Titan\BookingSidecar\Dispatcher;

final class OwnerCancelGuardPdo extends PDO
{
    public int $touches = 0;

    private function touch(): never
    {
        $this->touches++;
        throw new RuntimeException('db reached');
    }

    public function getAttribute(int $attribute): mixed
    {
        $this->touch();
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false
    {
        $this->touch();
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->touch();
    }

    public function beginTransaction(): bool
    {
        $this->touch();
    }

    public function inTransaction(): bool
    {
        $this->touch();
    }
}

$ownerCancelFlags = [
    'booking_writes_enabled' => true,
    'booking_schema_verified' => true,
    'booking_rate_mapping_verified' => true,
    'booking_concurrency_verified' => true,
    'owner_operations_enabled' => true,
    'authenticated_owner_id' => 12,
    'database' => 'titan_booking_test_a',
    'verified_pricing_settings' => [],
];

$ownerCancelPayload = static fn (array $extra = []): array => array_merge(
    ['booking_id' => 42, 'idempotency_key' => 'cancel-key-owner-guard-0000001', 'reason' => 'Client unavailable'],
    $extra
);

/** @return array{0: ?\Throwable, 1: int} the exception (if any) and how many times the database was touched */
$ownerCancelOutcome = static function (array $payload) use ($ownerCancelFlags): array {
    $db = (new ReflectionClass(OwnerCancelGuardPdo::class))->newInstanceWithoutConstructor();
    $error = null;
    try {
        (new Dispatcher($db, $ownerCancelFlags))->dispatch('owner_booking_cancel', $payload);
    } catch (\Throwable $e) {
        $error = $e;
    }

    return [$error, $db->touches];
};

// Unguarded submissions on the owner route are refused, first time and when repeated, before any database access.
foreach (['authenticated_owner_id', 'owner_id', 'tenant_id', 'database', 'price', 'total', 'status', 'payment_status', 'owner_operations_enabled', 'verified_pricing_settings'] as $field) {
    [$first, $touchedFirst] = $ownerCancelOutcome($ownerCancelPayload([$field => 'x']));
    [$repeat, $touchedRepeat] = $ownerCancelOutcome($ownerCancelPayload([$field => 'x']));
    check($first instanceof InvalidArgumentException, "owner cancel must refuse privileged field {$field}");
    check($repeat instanceof InvalidArgumentException, "owner cancel must refuse repeated privileged field {$field}");
    check($touchedFirst + $touchedRepeat === 0, "owner cancel with privileged field {$field} must not touch the database");
}

// A valid owner cancel (booking_id, idempotency_key, reason only) is not refused by the guard and still reaches the database.
[$valid, $touchedValid] = $ownerCancelOutcome($ownerCancelPayload());
check(!($valid instanceof InvalidArgumentException) && $touchedValid === 1, 'valid owner cancel must pass the guard and reach the database as before');
