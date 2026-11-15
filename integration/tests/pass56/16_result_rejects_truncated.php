<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{file_put_contents($d.'/result-a.json','{');expectFailure(fn()=>ConcurrencyResultStore::read($d,'a'));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 16_result_rejects_truncated\n';
