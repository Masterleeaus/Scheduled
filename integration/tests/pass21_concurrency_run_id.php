<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$id=ConcurrencyRunId::make();ConcurrencyRunId::assert($id);check(strlen($id)===32);expectFailure(fn()=>ConcurrencyRunId::assert('../bad'));
echo "PASS concurrency_run_id\n";
