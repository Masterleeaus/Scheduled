<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 $prefix='Titan\\BookingSidecar\\';if(str_starts_with($class,$prefix)){$name=substr($class,strlen($prefix));$file=$root.'/private/booking-sidecar/'.$name.'.php';if(is_file($file))require_once $file;}
});
function verify(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function rejected(callable $fn):void{try{$fn();}catch(InvalidArgumentException|DomainException|LogicException $e){return;}throw new RuntimeException('Expected rejection');}

$actual=Titan\BookingSidecar\BookingLiveWriteColumns::required();$actual['ct_bookings']=array_diff($actual['ct_bookings'],['staff_ids']);verify(in_array('ct_bookings.staff_ids',Titan\BookingSidecar\BookingLiveWriteColumns::missing($actual),true),'missing staff');
echo "PASS 02_missing_booking_column.php\n";
