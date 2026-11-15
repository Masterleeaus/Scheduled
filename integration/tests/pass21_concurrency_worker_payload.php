<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$id=ConcurrencyRunId::make();$a=ConcurrencyWorkerPayload::build(validFixture(),$id,'a');$b=ConcurrencyWorkerPayload::build(validFixture(),$id,'b');check($a['idempotency_key']!==$b['idempotency_key']);check($a['start_at']===$b['start_at']);
echo "PASS concurrency_worker_payload\n";
