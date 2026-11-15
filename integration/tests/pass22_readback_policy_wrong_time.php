<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
$id=ConcurrencyFixtureIdentity::from(validFixture());expectFailure(fn()=>ConcurrencyReadbackPolicy::assert(['id'=>9,'service_id'=>1,'booking_date_time'=>'2027-01-10 11:00:00','staff_ids'=>'1','booking_status'=>'A'],9,$id));
echo 'PASS pass22_readback_policy_wrong_time\n';
