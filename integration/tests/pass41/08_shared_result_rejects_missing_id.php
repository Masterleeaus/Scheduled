<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
foreach([-1,0] as $id){try{Titan\BookingSidecar\BookingSharedResult::bookingId($id);throw new Exception('accepted');}catch(DomainException $e){}}if(Titan\BookingSidecar\BookingSharedResult::bookingId(42)!==42)throw new Exception('changed ID');
echo "PASS pass41_shared_result_rejects_missing_id\n";
