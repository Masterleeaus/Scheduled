<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
$r=['id'=>'7','booking_status'=>'A','staff_ids'=>'3','booking_date_time'=>'2026-11-10 09:00:00','order_duration'=>'-1'];rejected(fn()=>B\BookingConflictRowInterval::parse($r,3,'Australia/Melbourne'));
echo 'PASS 09_occupied_row_bad_duration.php\n';
