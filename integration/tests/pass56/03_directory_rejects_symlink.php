<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyDirectory,ConcurrencyRunId,ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyProcess};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());$alias=sys_get_temp_dir().'/titan-alias-'.bin2hex(random_bytes(4));try{check(symlink($d,$alias));expectFailure(fn()=>ConcurrencyProtocol::directory($alias));}finally{@unlink($alias);ConcurrencyDirectory::clear($d);}
echo 'PASS 03_directory_rejects_symlink\n';
