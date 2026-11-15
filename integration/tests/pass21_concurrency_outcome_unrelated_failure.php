<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
expectFailure(fn()=>ConcurrencyOutcome::assertSingleWinner(ConcurrencyResult::accepted(3),ConcurrencyResult::rejected(new DomainException('SQL credentials invalid'))));
echo "PASS concurrency_outcome_unrelated_failure\n";
