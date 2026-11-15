<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{file_put_contents($d.'/GO','wrong');expectFailure(fn()=>ConcurrencyBarrier::awaitGo($d,20));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 07_barrier_corrupt_go\n';
