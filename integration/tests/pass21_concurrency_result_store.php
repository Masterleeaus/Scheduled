<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
use Titan\BookingIntegration\{AcceptanceTestSettings,ConcurrencyFixturePolicy,ConcurrencyRunId,ConcurrencyBarrier,ConcurrencyDirectory,ConcurrencyWorkerPayload,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyOutcome,ConcurrencyAcceptanceReport,ConcurrencySupervisor,ConcurrencyProcess};
$dir=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyResultStore::write($dir,'a',ConcurrencyResult::accepted(33));check(ConcurrencyResultStore::read($dir,'a')['booking_id']===33);}finally{ConcurrencyDirectory::clear($dir);}
echo "PASS concurrency_result_store\n";
