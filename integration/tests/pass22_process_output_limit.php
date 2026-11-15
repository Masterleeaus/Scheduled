<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDeadline,ConcurrencyWorkerExit,ConcurrencyProcessOutput,ConcurrencyFixtureIdentity,ConcurrencyReadbackPolicy,ConcurrencyOutcomeEvidence,ConcurrencyFailureCategory,ConcurrencyOutcome,ConcurrencyResult,ConcurrencyResultGuard,ConcurrencyEvidenceReport};
check(ConcurrencyProcessOutput::append('ab','cd',1024)==='abcd');expectFailure(fn()=>ConcurrencyProcessOutput::append(str_repeat('x',1024),'y',1024));
echo 'PASS pass22_process_output_limit\n';
