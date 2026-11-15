<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
$paths=['BookingSharedTransactionWriter.php','BookingSharedPersist.php','BookingSharedAvailability.php'];foreach($paths as $p){$src=file_get_contents($root.'/private/booking-sidecar/'.$p);foreach(['beginTransaction(', 'commit(', 'rollBack(', 'BookingRequestLedger('] as $bad){if(str_contains($src,$bad))throw new Exception($p.' contains '.$bad);}}
echo "PASS pass41_no_second_transaction\n";
