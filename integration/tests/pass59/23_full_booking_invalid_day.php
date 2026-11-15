<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {$b=p59_booking();$b['booking_date_time']='2027-04-31 09:00:00';LifecycleFullBookingRow::parse($b);});
echo "PASS59 23_full_booking_invalid_day\n";
