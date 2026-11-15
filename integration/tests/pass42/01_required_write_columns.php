<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 $prefix='Titan\\BookingSidecar\\';if(str_starts_with($class,$prefix)){$name=substr($class,strlen($prefix));$file=$root.'/private/booking-sidecar/'.$name.'.php';if(is_file($file))require_once $file;}
});
function verify(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function rejected(callable $fn):void{try{$fn();}catch(InvalidArgumentException|DomainException|LogicException $e){return;}throw new RuntimeException('Expected rejection');}

$tables=Titan\BookingSidecar\BookingLiveWriteColumns::required();verify(in_array('booking_status',$tables['ct_bookings'],true),'status');verify(in_array('client_phone',$tables['ct_order_client_info'],true),'phone');
echo "PASS 01_required_write_columns.php\n";
