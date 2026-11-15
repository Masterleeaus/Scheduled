<?php
use Titan\BookingSidecar\{BookingWriteProofId,BookingWriteProofStaff,BookingWriteProofService,BookingWriteProofStatus,BookingWriteProofDuration,BookingWriteProofRate,BookingWriteProofTime,BookingWriteProofCustomer,BookingWriteProofLink,BookingWriteProofRows};
require __DIR__.'/bootstrap.php';
pass44_expect(static function():void { BookingWriteProofService::check(array_replace(pass44_booking(),["method_unit_qty"=>"2.5"]),pass44_command()["service"]); }, true);
echo "PASS 21_quantity_fraction_reject\n";
