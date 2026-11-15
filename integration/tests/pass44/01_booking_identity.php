<?php
use Titan\BookingSidecar\{BookingWriteProofId,BookingWriteProofStaff,BookingWriteProofService,BookingWriteProofStatus,BookingWriteProofDuration,BookingWriteProofRate,BookingWriteProofTime,BookingWriteProofCustomer,BookingWriteProofLink,BookingWriteProofRows};
require __DIR__.'/bootstrap.php';
pass44_expect(static function():void { BookingWriteProofId::check("17")===17; }, false);
echo "PASS 01_booking_identity\n";
