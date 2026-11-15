<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
$e=ConcurrencyOutcomeEvidence::merge(['passed'=>true,'accepted_booking_id'=>9],['persisted'=>true,'booking_id'=>9]);check($e['database_readback']['booking_id']===9);
echo 'PASS pass22_outcome_evidence\n';
