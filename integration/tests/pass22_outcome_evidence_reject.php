<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
expectFailure(fn()=>ConcurrencyOutcomeEvidence::merge(['passed'=>true,'accepted_booking_id'=>9],['persisted'=>true,'booking_id'=>8]));
echo 'PASS pass22_outcome_evidence_reject\n';
