<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
App\Booking\Public\Redemption\CommitCleantoLoader::register($root.'/private/booking-sidecar');if(!class_exists('Titan\BookingSidecar\BookingSharedTransactionWriter'))throw new Exception('class missing');if(!class_exists('Titan\BookingSidecar\BookingFinalAvailability'))throw new Exception('availability missing');
echo "PASS pass41_loader_imports_originals\n";
