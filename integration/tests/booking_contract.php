<?php

require dirname(__DIR__, 2) . '/core/app/Booking/BookingPayload.php';
require dirname(__DIR__, 2) . '/core/app/Booking/BookingEnvelopeSigner.php';

use App\Booking\BookingPayload;
use App\Booking\BookingEnvelopeSigner;

function verify(bool $predicate, string $label): void {
    if (!$predicate) { throw new RuntimeException('FAIL: ' . $label); }
    echo "PASS: $label\n";
}
verify(BookingPayload::slots(['date' => '2026-11-12', 'service_id' => '15']) === ['date' => '2026-11-12', 'service_id' => 15], 'valid slot input');
foreach (['2026-02-30', '2026-13-01', '2026-1-1'] as $invalid) {
    try { BookingPayload::slots(['date' => $invalid, 'service_id' => 1]); throw new RuntimeException('accepted bad date'); }
    catch (InvalidArgumentException $e) { verify(true, 'reject ' . $invalid); }
}
$secret = str_repeat('x', 40);
$nonce = bin2hex(random_bytes(16));
$sig = BookingEnvelopeSigner::sign($secret, 'tenant-1', '{"a":1}', 1000, $nonce);
verify(BookingEnvelopeSigner::verify($secret, 'tenant-1', '{"a":1}', 1000, $nonce, $sig, 1020), 'valid signed envelope');
verify(!BookingEnvelopeSigner::verify($secret, 'tenant-2', '{"a":1}', 1000, $nonce, $sig, 1020), 'tenant mismatch rejected');
verify(!BookingEnvelopeSigner::verify($secret, 'tenant-1', '{"a":2}', 1000, $nonce, $sig, 1020), 'tampered body rejected');
verify(!BookingEnvelopeSigner::verify($secret, 'tenant-1', '{"a":1}', 1000, $nonce, $sig, 2000), 'expired envelope rejected');
