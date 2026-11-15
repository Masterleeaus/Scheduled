<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {$b=p59_booking();$b['client_id']='2147483648';LifecycleFullBookingRow::parse($b);});
echo "PASS59 22_full_booking_overflow\n";
