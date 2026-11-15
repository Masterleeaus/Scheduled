<?php
declare(strict_types=1);
require __DIR__.'/pass81_worker_runtime.php';
use Titan\BookingIntegration\{ConcurrencySupervisor,ConcurrencyDirectory};
function scenario82(callable $body):void {
 $dir=ConcurrencyDirectory::create(bin2hex(random_bytes(16)));
 $handles=[];
 try {$body($dir,$handles);} finally {
  foreach($handles as $h)stop81($h);
  if(is_dir($dir))ConcurrencyDirectory::clear($dir);
 }
}
// Real workers are required; two pre-existing READY files must not overrule dead processes.
scenario82(function($dir,&$handles){
 file_put_contents($dir.'/ready-a','ready');file_put_contents($dir.'/ready-b','ready');
 $handles=[spawn81('exit(0);'),spawn81('exit(0);')];usleep(120000);
 failure81(fn()=>ConcurrencySupervisor::waitReady($dir,$handles,500),'Worker exited');
 check(!file_exists($dir.'/GO'));
});
// The release gate independently rechecks liveness after waitReady.
scenario82(function($dir,&$handles){
 file_put_contents($dir.'/ready-a','ready');file_put_contents($dir.'/ready-b','ready');
 $handles=[spawn81('usleep(2000000);'),spawn81('usleep(2000000);')];
 ConcurrencySupervisor::waitReady($dir,$handles,500);
 ConcurrencySupervisor::release($dir,$handles);
 check(file_get_contents($dir.'/GO')==='go');
 failure81(fn()=>ConcurrencySupervisor::release($dir,$handles),'already released');
});
scenario82(function($dir,&$handles){
 $h=spawn81('usleep(2000000);');$handles=[$h];
 failure81(fn()=>ConcurrencySupervisor::waitReady($dir,[$h,$h],200),'Duplicate worker handle');
 check(!file_exists($dir.'/GO'));
});
scenario82(function($dir,&$handles){
 $handles=[spawn81('usleep(2000000);'),spawn81('usleep(2000000);')];
 file_put_contents($dir.'/ready-a','ready');file_put_contents($dir.'/ready-b','corrupt');
 failure81(fn()=>ConcurrencySupervisor::waitReady($dir,$handles,150),'ready barrier');
 check(!file_exists($dir.'/GO'));
});
scenario82(function($dir,&$handles){
 file_put_contents($dir.'/ready-a','ready');file_put_contents($dir.'/ready-b','ready');
 $handles=[spawn81('exit(0);'),spawn81('usleep(2000000);')];usleep(120000);
 failure81(fn()=>ConcurrencySupervisor::release($dir,$handles),'Worker exited');
 check(!file_exists($dir.'/GO'));
});
echo "PASS pass82_barrier_runtime: 5 subprocess scenarios\n";
