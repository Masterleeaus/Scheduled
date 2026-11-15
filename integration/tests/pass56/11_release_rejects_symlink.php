<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());$target=tempnam(sys_get_temp_dir(),'p56');try{check(symlink($target,$d.'/GO'));expectFailure(fn()=>ConcurrencySupervisor::release($d));}finally{unlink($d.'/GO');unlink($target);ConcurrencyDirectory::clear($d);}
echo 'PASS 11_release_rejects_symlink\n';
