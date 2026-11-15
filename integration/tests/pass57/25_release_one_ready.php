<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyBarrier::ready($d,'a');expectFailure(fn()=>ConcurrencySupervisor::release($d));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 25_release_one_ready\n';
