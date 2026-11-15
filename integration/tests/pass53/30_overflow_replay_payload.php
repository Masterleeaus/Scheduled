<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject53(static function():void { \Titan\BookingSidecar\BookingReplayPayloadGuard::bookingId(['state'=>'completed','booking_id'=>'2147483648']); }, 'booking');
echo 'PASS53 30_overflow_replay_payload OK';
