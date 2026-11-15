<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$id=ConcurrencyRunId::make();$f=validFixture();$a=ConcurrencyWorkerPayload::build($f,$id,'a');check(!isset($f['create']['idempotency_key']) && $a['staff_ids']===$f['create']['staff_ids']);
echo "PASS concurrency_isolation_immutable\n";
