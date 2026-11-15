<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
assert53(\Titan\BookingSidecar\BookingReplayPayloadGuard::bookingId(['state'=>'completed','booking_id'=>'17'])===17,'ID mismatch');
echo 'PASS53 31_valid_replay_payload OK';
