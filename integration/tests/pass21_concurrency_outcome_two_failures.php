<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
expectFailure(fn()=>ConcurrencyOutcome::assertSingleWinner(ConcurrencyResult::rejected(new DomainException('Conflict')),ConcurrencyResult::rejected(new DomainException('Conflict'))));
echo "PASS concurrency_outcome_two_failures\n";
