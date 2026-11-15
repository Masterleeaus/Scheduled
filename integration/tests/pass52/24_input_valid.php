<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$v=\Titan\BookingSidecar\BookingCancellationInput::parse(['booking_id'=>7,'idempotency_key'=>str_repeat('a',24),'reason'=>'request']);ok52($v['booking_id']===7&&$v['reason']==='request','valid cancellation');
echo 'PASS52 24_input_valid OK';
