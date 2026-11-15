<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$n=0;$x=new \App\Booking\Public\Redemption\CommitLegacyLockBoundary(function($db,$s)use(&$n){$n++;},function($db)use(&$n){$n++;});$db=(new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();$x->before($db,[]);$x->after($db);if($n!==2)throw new Exception('lease');
echo "PASS\n";
