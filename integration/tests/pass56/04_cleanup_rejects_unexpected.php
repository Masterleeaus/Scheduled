<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());file_put_contents($d.'/unexpected.txt','x');try{expectFailure(fn()=>ConcurrencyDirectory::clear($d));}finally{unlink($d.'/unexpected.txt');ConcurrencyDirectory::clear($d);}
echo 'PASS 04_cleanup_rejects_unexpected\n';
