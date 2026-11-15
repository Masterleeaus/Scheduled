<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$c=\App\Booking\Public\Redemption\CommitLegacyCustomer::map(['name'=>'Test','email'=>'test@example.com','phone'=>'0400000000']);if($c['phone']!=='0400000000')throw new Exception('customer');
echo "PASS\n";
