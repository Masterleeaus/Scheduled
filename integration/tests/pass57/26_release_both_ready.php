<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{ConcurrencyProcessOutput,ConcurrencyProcessGroup,ConcurrencyDirectory,ConcurrencyRunId,ConcurrencySupervisor,ConcurrencyProtocol,ConcurrencyBarrier};
$d=ConcurrencyDirectory::create(ConcurrencyRunId::make());try{ConcurrencyBarrier::ready($d,'a');ConcurrencyBarrier::ready($d,'b');ConcurrencySupervisor::release($d);check(ConcurrencyProtocol::marker($d,'GO','go'));}finally{ConcurrencyDirectory::clear($d);}
echo 'PASS 26_release_both_ready\n';
