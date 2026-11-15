<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());$target=tempnam(sys_get_temp_dir(),'p56');try{check(symlink($target,$d.'/result-a.json'));expectFailure(fn()=>ConcurrencyResultStore::read($d,'a'));expectFailure(fn()=>ConcurrencyResultStore::write($d,'a',ConcurrencyResult::accepted(1)));}finally{unlink($d.'/result-a.json');unlink($target);ConcurrencyDirectory::clear($d);}
echo 'PASS 14_result_rejects_symlink\n';
