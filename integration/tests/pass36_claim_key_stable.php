<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$a=\App\Booking\Public\Redemption\CommitLegacyClaimKey::derive(str_repeat('x',24));$b=\App\Booking\Public\Redemption\CommitLegacyClaimKey::derive(str_repeat('x',24));if($a!==$b||strlen($a)!==64)throw new Exception('key');
echo "PASS\n";
