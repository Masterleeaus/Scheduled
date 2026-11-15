<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$f=file_get_contents(__DIR__.'/../../core/app/Booking/Public/Redemption/CommitOrchestrator.php');if(strpos($f,'$before($db,$selection)')>strpos($f,'RedeemTransaction::run'))throw new Exception('locks acquired too late');if(!str_contains($f,'finally {if($acquired'))throw new Exception('locks not released');
echo "PASS\n";
