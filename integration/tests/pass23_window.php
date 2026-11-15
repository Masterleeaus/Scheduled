<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedStaffSet,PersistedStatusPolicy,PersistedInterval,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedSingleWinner,PersistedScanPolicy,PersistedOverlapQuery,PersistedEvidenceGate,ConcurrencyEvidenceReport};
function row():array{return ['id'=>9,'booking_date_time'=>'2027-01-10 09:00:00','staff_ids'=>'1','booking_status'=>'A','order_duration'=>60];}
$w=PersistedFixtureWindow::from(validFixture());check($w['staff_ids']===[1]);check($w['interval'][1]-$w['interval'][0]===3600);
echo "PASS pass23_window.php\n";
