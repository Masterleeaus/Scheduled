<?php
declare(strict_types=1);
$dir=dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/';
foreach(['CommitBookingId','CommitBookingStatusPolicy','CommitEvidence'] as $f)require_once $dir.$f.'.php';
$c='App\Booking\Public\Redemption\CommitEvidence';
foreach(['X','',null,'D'] as $status){try{$c::assert(['id'=>7,'client_id'=>3,'order_id'=>8,'booking_date_time'=>'2026-11-10 09:00:00','booking_status'=>$status],7);exit(1);}catch(RuntimeException $e){}}
echo "PASS inactive_status_behavior
";
