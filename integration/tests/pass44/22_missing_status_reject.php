<?php
use Titan\BookingSidecar\{BookingWriteProofId,BookingWriteProofStaff,BookingWriteProofService,BookingWriteProofStatus,BookingWriteProofDuration,BookingWriteProofRate,BookingWriteProofTime,BookingWriteProofCustomer,BookingWriteProofLink,BookingWriteProofRows};
require __DIR__.'/bootstrap.php';
pass44_expect(static function():void { BookingWriteProofRows::check(array_replace(pass44_booking(),["booking_status"=>null]),pass44_order(),17,pass44_command(),"50.00"); }, true);
echo "PASS 22_missing_status_reject\n";
