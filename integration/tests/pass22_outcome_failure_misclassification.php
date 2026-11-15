<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
expectFailure(fn()=>ConcurrencyOutcome::assertSingleWinner(ConcurrencyResult::accepted(1),ConcurrencyResult::rejected(new DomainException('SQL connection conflict'))));
echo 'PASS pass22_outcome_failure_misclassification\n';
