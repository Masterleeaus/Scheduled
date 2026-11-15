<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());mkdir($d.'/sub');try{expectFailure(fn()=>ConcurrencyDirectory::clear($d));}finally{rmdir($d.'/sub');ConcurrencyDirectory::clear($d);}
echo 'PASS 05_cleanup_rejects_nested\n';
