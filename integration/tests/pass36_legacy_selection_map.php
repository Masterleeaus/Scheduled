<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$s=['service_id'=>1,'method_id'=>2,'unit_id'=>3,'staff_id'=>4,'quantity'=>2,'date_time'=>'2026-12-15 09:00:00','duration_minutes'=>60,'timezone'=>'Australia/Melbourne'];$m=\App\Booking\Public\Redemption\CommitLegacySelection::map($s,['name'=>'X','email'=>'x@example.com','phone'=>'1234567890'],str_repeat('a',64));if($m['service']['method_unit_id']!==3||$m['staff_ids']!==[4])throw new Exception('mapping');
echo "PASS\n";
