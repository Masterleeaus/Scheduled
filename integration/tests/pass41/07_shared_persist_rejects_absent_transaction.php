<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
if(!class_exists("Pass41FakePDO",false)){class Pass41FakePDO extends PDO {public function __construct(){} public function inTransaction():bool{return false;}}}$db=new Pass41FakePDO();try{Titan\BookingSidecar\BookingSharedPersist::write($db,[],'0.00');throw new Exception('accepted');}catch(LogicException $e){}
echo "PASS pass41_shared_persist_rejects_absent_transaction\n";
