<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{PersistedLocalClock,PersistedWindowBounds,PersistedInterval,PersistedOverlapQuery,PersistedFixtureWindow,PersistedRowPolicy,PersistedOverlapCounter,PersistedEvidenceConsistency,PersistedSingleWinner,PersistedStatusPolicy,PersistedStaffSet,PersistedConcurrencyEvidence};
expectFailure(fn()=>PersistedEvidenceConsistency::assert(['persisted'=>true,'overlap_scan_passed'=>true,'overlapping_booking_ids'=>[1],'overlapping_booking_count'=>1],1));
echo "PASS pass24_evidence_no_scan_count.php\n";
