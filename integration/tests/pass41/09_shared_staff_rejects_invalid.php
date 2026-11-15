<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 foreach(['App\\Booking\\Public\\Redemption\\'=>'core/app/Booking/Public/Redemption/','Titan\\BookingSidecar\\'=>'private/booking-sidecar/'] as $prefix=>$path){
  if(str_starts_with($class,$prefix)){$file=$root.'/'.$path.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;return;}
 }
});
if(!class_exists("Pass41FakePDO",false)){class Pass41FakePDO extends PDO {public function __construct(){} public function inTransaction():bool{return false;}}}$db=new Pass41FakePDO();foreach([0,-1,'1',null] as $id){try{Titan\BookingSidecar\BookingSharedStaffLocks::keys($db,['staff_id'=>$id]);throw new Exception('accepted');}catch(InvalidArgumentException $e){}}
echo "PASS pass41_shared_staff_rejects_invalid\n";
