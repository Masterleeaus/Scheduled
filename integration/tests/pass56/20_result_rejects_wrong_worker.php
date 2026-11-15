<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{expectFailure(fn()=>ConcurrencyResultStore::write($d,'../escape',ConcurrencyResult::accepted(1)));expectFailure(fn()=>ConcurrencyResultStore::read($d,'c'));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 20_result_rejects_wrong_worker\n';
