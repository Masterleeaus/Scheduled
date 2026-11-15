<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$r=ConcurrencyResult::accepted(42);ConcurrencyResult::assertShape($r);check($r['booking_id']===42);expectFailure(fn()=>ConcurrencyResult::accepted(0));
echo "PASS concurrency_result_success\n";
