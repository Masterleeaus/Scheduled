<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedStaffSet,PersistedStatusPolicy,PersistedInterval,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedSingleWinner,PersistedScanPolicy,PersistedOverlapQuery,PersistedEvidenceGate,ConcurrencyEvidenceReport};
function row():array{return ['id'=>9,'booking_date_time'=>'2027-01-10 09:00:00','staff_ids'=>'1','booking_status'=>'A','order_duration'=>60];}
expectFailure(fn()=>PersistedEvidenceGate::assert(['persisted'=>true,'overlap_scan_passed'=>true,'overlapping_booking_ids'=>[9,10],'overlapping_booking_count'=>2],9));
echo "PASS pass23_evidence_gate_reject.php\n";
