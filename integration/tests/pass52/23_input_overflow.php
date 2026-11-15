<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>\Titan\BookingSidecar\BookingCancellationInput::parse(['booking_id'=>2147483648,'idempotency_key'=>str_repeat('a',24)]),'Invalid booking ID');
echo 'PASS52 23_input_overflow OK';
