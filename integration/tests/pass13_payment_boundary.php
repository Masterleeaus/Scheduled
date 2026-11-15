<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
$created=\Titan\BookingSidecar\BookingPaymentBoundary::forBooking(['booking_id'=>5,'order_id'=>7,'replayed'=>false]);
if($created['payment_status']!=='unpaid'||!$created['requires_payment_processing'])throw new Exception('Booking creation incorrectly treated as settled');
$replay=\Titan\BookingSidecar\BookingPaymentBoundary::forBooking(['booking_id'=>5,'order_id'=>7,'replayed'=>true]);
if($replay['payment_status']!=='unknown'||$replay['requires_payment_processing']||!$replay['payment_reconciliation_required'])throw new Exception('Replay payment reconciliation bypassed');
echo "PASS ".basename(__FILE__)."\n";
