<?php
use Titan\BookingSidecar\{BookingWriteProofId,BookingWriteProofStaff,BookingWriteProofService,BookingWriteProofStatus,BookingWriteProofDuration,BookingWriteProofRate,BookingWriteProofTime,BookingWriteProofCustomer,BookingWriteProofLink,BookingWriteProofRows};
require __DIR__.'/bootstrap.php';
pass44_expect(static function():void { BookingWriteProofCustomer::check(array_replace(pass44_order(),["client_email"=>"x@example.org"]),pass44_command()["customer"]); }, true);
echo "PASS 16_customer_reject\n";
