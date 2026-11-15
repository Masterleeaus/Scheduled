<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$dir=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyBarrier::ready($dir,'a');ConcurrencyBarrier::ready($dir,'b');ConcurrencySupervisor::release($dir);check(is_file($dir.'/GO'));}finally{ConcurrencyDirectory::clear($dir);}
echo "PASS concurrency_supervisor_release\n";
