<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
$x=ConcurrencyFixtureIdentity::from(validFixture());check($x['service_id']===1 && $x['staff_ids']===[1]);
echo 'PASS pass22_fixture_identity\n';
