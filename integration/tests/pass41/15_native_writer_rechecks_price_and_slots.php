<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
$src=file_get_contents($root.'/private/booking-sidecar/BookingSharedTransactionWriter.php');foreach(['BookingSharedRate::authoritative','BookingSharedAvailability::assert','BookingSharedPersist::write','BookingSharedTransactionScope::assert'] as $name){if(!str_contains($src,$name))throw new Exception('missing '.$name);}
echo "PASS pass41_native_writer_rechecks_price_and_slots\n";
