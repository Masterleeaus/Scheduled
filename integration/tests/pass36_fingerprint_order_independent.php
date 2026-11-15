<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$s=['service_id'=>1,'method_id'=>2,'unit_id'=>3,'staff_id'=>4,'quantity'=>1,'date_time'=>'2026-12-15 09:00:00','duration_minutes'=>60,'timezone'=>'Australia/Melbourne'];$c=['name'=>'Test','email'=>'test@example.com','phone'=>'0400000000'];$a=\App\Booking\Public\Redemption\CommitLegacyFingerprint::make(5,$s,$c);$b=\App\Booking\Public\Redemption\CommitLegacyFingerprint::make(5,array_reverse($s,true),$c);if($a!==$b)throw new Exception('fingerprint');
echo "PASS\n";
