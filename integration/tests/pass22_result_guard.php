<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
expectFailure(fn()=>ConcurrencyResultGuard::assertDistinct(['outcome'=>'accepted','booking_id'=>1],['outcome'=>'accepted','booking_id'=>1]));
echo 'PASS pass22_result_guard\n';
