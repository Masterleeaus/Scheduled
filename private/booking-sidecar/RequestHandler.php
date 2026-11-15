<?php
namespace Titan\BookingSidecar;
use InvalidArgumentException;
use RuntimeException;
/** Same HMAC wire contract as PlusAgency; zero tenant DB information from request body. */
final class RequestHandler {
    public function __construct(private TenantSecrets $registry, private NonceLedger $ledger, private string $secret) {}
    public function handle(string $body, array $headers, int $now): array {
        $key = $headers['x-booking-instance'] ?? '';
        $config = $this->registry->get($key);
        $nonce = Envelope::verify($this->secret, $key, $body, $headers, $now);
        $this->ledger->reserve($key, $nonce, $now);
        $request = json_decode($body, true, 64, JSON_THROW_ON_ERROR);
        if (!is_array($request) || !is_string($request['operation'] ?? null) || !is_array($request['payload'] ?? null)) throw new InvalidArgumentException('Invalid request schema.');
        $operation = $request['operation'];
        if (!in_array($operation, ['health','services','booking_catalogue','busy_intervals','availability','booking_preview','booking_preview_capabilities','owner_booking_list','owner_booking_get','owner_booking_create','owner_booking_cancel','owner_booking_reschedule','owner_booking_summary','owner_service_list','owner_service_create','owner_service_update','owner_service_units','owner_service_unit_update','owner_staff_overview','owner_staff_service_assignment','owner_staff_schedule_update'], true)) throw new InvalidArgumentException('Operation unavailable.');
        $db = new \PDO($config['dsn'], $config['db_user'], $config['db_password'], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_EMULATE_PREPARES => false]);
        return (new Dispatcher($db, $config))->dispatch($operation, $request['payload']);
    }
}
