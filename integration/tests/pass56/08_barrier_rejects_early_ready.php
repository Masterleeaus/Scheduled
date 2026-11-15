<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyBarrier::ready($d,'a');ConcurrencyBarrier::ready($d,'b');ConcurrencySupervisor::release($d);expectFailure(fn()=>ConcurrencyBarrier::ready($d,'a'));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 08_barrier_rejects_early_ready\n';
