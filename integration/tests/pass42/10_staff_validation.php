<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 $prefix='Titan\\BookingSidecar\\';if(str_starts_with($class,$prefix)){$name=substr($class,strlen($prefix));$file=$root.'/private/booking-sidecar/'.$name.'.php';if(is_file($file))require_once $file;}
});
function verify(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function rejected(callable $fn):void{try{$fn();}catch(InvalidArgumentException|DomainException|LogicException $e){return;}throw new RuntimeException('Expected rejection');}

Titan\BookingSidecar\BookingLiveWriteStaff::assert([1,2,3]);rejected(fn()=>Titan\BookingSidecar\BookingLiveWriteStaff::assert([2,2]));rejected(fn()=>Titan\BookingSidecar\BookingLiveWriteStaff::assert([]));
echo "PASS 10_staff_validation.php\n";
