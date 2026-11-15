<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
$r=['id'=>7,'booking_status'=>'A','staff_ids'=>'3','booking_date_time'=>'2026-11-10 09:00:00','order_duration'=>'60'];rejected(fn()=>B\BookingConflictRows::occupied(array_fill(0,5001,$r),3,'Australia/Melbourne',1,2));
echo 'PASS 14_truncated_rows.php\n';
