<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
$r=['id'=>'7','booking_status'=>'A','staff_ids'=>'3','booking_date_time'=>'2026-11-10 09:00:00','order_duration'=>'120'];$a=B\BookingConflictRowTime::instant('2026-11-10 10:00:00','Australia/Melbourne');verify(count(B\BookingConflictRows::occupied([$r],3,'Australia/Melbourne',$a,$a+3600))===1,'overlap');
echo 'PASS 13_readback_detects_overlap.php\n';
