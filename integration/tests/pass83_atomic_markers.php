<?php
declare(strict_types=1);
require __DIR__.'/pass81_worker_runtime.php';
use Titan\BookingIntegration\{ConcurrencyProtocol,ConcurrencyBarrier,ConcurrencySupervisor,ConcurrencyDirectory};
$checks=0;
function test83(callable $callback):void {global $checks;$dir=ConcurrencyDirectory::create(bin2hex(random_bytes(16)));try{$callback($dir);$checks++;}finally{ConcurrencyDirectory::clear($dir);}}
test83(function($dir){ConcurrencyProtocol::createMarker($dir,'ready-a','ready');check(file_get_contents($dir.'/ready-a')==='ready');failure81(fn()=>ConcurrencyProtocol::createMarker($dir,'ready-a','bad'),'already exists');check(file_get_contents($dir.'/ready-a')==='ready');});
test83(function($dir){file_put_contents($dir.'/target','untouched');symlink($dir.'/target',$dir.'/ready-a');failure81(fn()=>ConcurrencyProtocol::createMarker($dir,'ready-a','bad'),'already exists');check(file_get_contents($dir.'/target')==='untouched');unlink($dir.'/ready-a');unlink($dir.'/target');});
test83(function($dir){failure81(fn()=>ConcurrencyProtocol::createMarker($dir,'../outside','oops'),'Unknown barrier');check(!file_exists(dirname($dir).'/outside'));});
test83(function($dir){ConcurrencyBarrier::ready($dir,'a');ConcurrencyBarrier::ready($dir,'a');check(file_get_contents($dir.'/ready-a')==='ready');});
test83(function($dir){ConcurrencyBarrier::ready($dir,'a');ConcurrencyBarrier::ready($dir,'b');ConcurrencySupervisor::release($dir);check(file_get_contents($dir.'/GO')==='go');failure81(fn()=>ConcurrencySupervisor::release($dir),'already released');});
test83(function($dir){ConcurrencyBarrier::ready($dir,'a');ConcurrencyBarrier::ready($dir,'b');file_put_contents($dir.'/GO','invalid');failure81(fn()=>ConcurrencySupervisor::release($dir),'already released');check(file_get_contents($dir.'/GO')==='invalid');});
echo "PASS pass83_atomic_markers: {$checks} cases\n";
