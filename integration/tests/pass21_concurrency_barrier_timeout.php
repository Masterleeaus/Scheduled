<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$dir=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{expectFailure(fn()=>ConcurrencyBarrier::awaitGo($dir,30));}finally{ConcurrencyDirectory::clear($dir);}
echo "PASS concurrency_barrier_timeout\n";
