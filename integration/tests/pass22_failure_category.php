<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
check(ConcurrencyFailureCategory::isConflict('Already booked'));check(!ConcurrencyFailureCategory::isConflict('SQL connection conflict'));check(!ConcurrencyFailureCategory::isConflict('Timeout'));
echo 'PASS pass22_failure_category\n';
