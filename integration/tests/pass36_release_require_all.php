<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$x=array_fill_keys(['public_writes_enabled','mysql_lifecycle_passed','concurrency_passed','pricing_parity_passed','tenant_isolation_passed','schema_passed'],true);\App\Booking\Public\Redemption\CommitLegacyRelease::require($x);
echo "PASS\n";
