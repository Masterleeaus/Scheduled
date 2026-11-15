<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
$id=ConcurrencyFixtureIdentity::from(validFixture());ConcurrencyReadbackPolicy::assert(['id'=>9,'service_id'=>1,'booking_date_time'=>$id['start_at'],'staff_ids'=>'1','booking_status'=>'A'],9,$id);
echo 'PASS pass22_readback_policy_valid\n';
