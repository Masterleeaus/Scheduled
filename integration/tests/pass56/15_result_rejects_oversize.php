<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{file_put_contents($d.'/result-a.json',str_repeat('a',4097));expectFailure(fn()=>ConcurrencyResultStore::read($d,'a'));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 15_result_rejects_oversize\n';
