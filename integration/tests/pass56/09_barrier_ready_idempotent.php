<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyBarrier::ready($d,'a');ConcurrencyBarrier::ready($d,'a');ConcurrencyBarrier::ready($d,'b');check(ConcurrencyBarrier::bothReady($d));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 09_barrier_ready_idempotent\n';
