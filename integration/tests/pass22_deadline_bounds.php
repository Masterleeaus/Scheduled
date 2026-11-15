<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
check(ConcurrencyDeadline::seconds(1000)===1.0);expectFailure(fn()=>ConcurrencyDeadline::seconds(10));expectFailure(fn()=>ConcurrencyDeadline::seconds(121000));
echo 'PASS pass22_deadline_bounds\n';
