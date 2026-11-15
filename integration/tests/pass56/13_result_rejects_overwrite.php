<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyResultStore::write($d,'a',ConcurrencyResult::accepted(123));expectFailure(fn()=>ConcurrencyResultStore::write($d,'a',ConcurrencyResult::accepted(456)));check(ConcurrencyResultStore::read($d,'a')['booking_id']===123);}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 13_result_rejects_overwrite\n';
