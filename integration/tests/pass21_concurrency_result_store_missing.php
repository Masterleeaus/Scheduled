<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$dir=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{expectFailure(fn()=>ConcurrencyResultStore::read($dir,'b'));}finally{ConcurrencyDirectory::clear($dir);}
echo "PASS concurrency_result_store_missing\n";
