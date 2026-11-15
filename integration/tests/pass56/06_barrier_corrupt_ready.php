<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{file_put_contents($d.'/ready-a','bad');ConcurrencyBarrier::ready($d,'b');check(!ConcurrencyBarrier::bothReady($d));expectFailure(fn()=>ConcurrencyBarrier::ready($d,'a'));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 06_barrier_corrupt_ready\n';
