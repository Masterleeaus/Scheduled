<?php
/** ENTRYPOINT CONTRACT: route POST /internal/bookings here in a dedicated PRIVATE sidecar host. */
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
use Titan\BookingSidecar\{RequestHandler, TenantSecrets, NonceLedger};
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || ($_SERVER['REQUEST_URI'] ?? '') !== '/internal/bookings') { http_response_code(404); echo '{"error":"Not found"}'; exit; }
$headers = [];
foreach (['instance','time','nonce','signature'] as $h) $headers['x-booking-'.$h] = (string)($_SERVER['HTTP_X_BOOKING_'.strtoupper($h)] ?? '');
try {
    $configPath = getenv('BOOKING_TENANTS_FILE') ?: '';
    $noncePath = getenv('BOOKING_NONCE_SQLITE') ?: '';
    $secret = getenv('BOOKING_INTERNAL_SECRET') ?: '';
    if ($configPath === '' || $noncePath === '' || strlen($secret) < 32) throw new RuntimeException('Private runtime not configured.');
    $ledger = new NonceLedger(new PDO('sqlite:' . $noncePath));
    $handler = new RequestHandler(new TenantSecrets($configPath), $ledger, $secret);
    $body = file_get_contents('php://input');
    if (!is_string($body) || strlen($body) > 65536) throw new InvalidArgumentException('Invalid request size.');
    echo json_encode($handler->handle($body, $headers, time()), JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    http_response_code($e instanceof InvalidArgumentException ? 400 : 503);
    error_log('Private booking service request rejected: ' . get_class($e));
    echo '{"error":"Booking request unavailable"}';
}
