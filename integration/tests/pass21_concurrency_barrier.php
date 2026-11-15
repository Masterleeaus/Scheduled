<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$dir=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyBarrier::ready($dir,'a');check(!ConcurrencyBarrier::bothReady($dir));ConcurrencyBarrier::ready($dir,'b');check(ConcurrencyBarrier::bothReady($dir));file_put_contents($dir.'/GO','go');ConcurrencyBarrier::awaitGo($dir,100);}finally{ConcurrencyDirectory::clear($dir);}
echo "PASS concurrency_barrier\n";
