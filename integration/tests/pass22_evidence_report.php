<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
$r=ConcurrencyEvidenceReport::attach(['passed'=>true],['database_readback'=>['persisted'=>true,'booking_id'=>5,'overlap_scan_passed'=>true,'overlapping_booking_ids'=>[5],'overlapping_booking_count'=>1]]);check($r['database_evidence']['booking_id']===5);
echo 'PASS pass22_evidence_report\n';
