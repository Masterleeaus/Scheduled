<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$x=new \App\Booking\Public\Redemption\CommitLegacyLockBoundary(fn()=>null,fn()=>null);$db=(new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();$x->before($db,[]);try{$x->before($db,[]);throw new Exception('accepted');}catch(RuntimeException $e){}$x->after($db);
echo "PASS\n";
