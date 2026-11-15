<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedStaffSet,PersistedStatusPolicy,PersistedInterval,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedSingleWinner,PersistedScanPolicy,PersistedOverlapQuery,PersistedEvidenceGate,ConcurrencyEvidenceReport};
function row():array{return ['id'=>9,'booking_date_time'=>'2027-01-10 09:00:00','staff_ids'=>'1','booking_status'=>'A','order_duration'=>60];}
$r=row();$r['booking_date_time']='2027-01-09 12:00:00';$r['order_duration']=1440;check(PersistedOverlapCounter::count([$r],PersistedFixtureWindow::from(validFixture()))===[9]);
echo "PASS pass23_overlap_long_started_earlier.php\n";
