<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{expectFailure(fn()=>ConcurrencySupervisor::waitReady($d,[[],[]],0));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 23_supervisor_invalid_timeout\n';
