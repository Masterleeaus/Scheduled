<?php
declare(strict_types=1);
spl_autoload_register(function($name){
 $prefix='App\\Booking\\Public\\Redemption\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../core/app/Booking/Public/Redemption/'.substr($name,strlen($prefix)).'.php';return;}
 $prefix='Titan\\BookingSidecar\\';
 if(str_starts_with($name,$prefix)){require __DIR__.'/../../private/booking-sidecar/'.substr($name,strlen($prefix)).'.php';}
});
$s=['service_id'=>1,'method_id'=>2,'unit_id'=>3,'staff_id'=>4,'quantity'=>1,'date_time'=>'2026-12-15 09:00:00'];$r=['service_id'=>'1','method_id'=>'2','method_unit_id'=>'3','method_unit_qty'=>'1','staff_ids'=>'9','booking_date_time'=>$s['date_time']];try{\App\Booking\Public\Redemption\CommitLegacyProof::verify($r,$s);throw new Exception('accepted');}catch(RuntimeException $e){}
echo "PASS\n";
