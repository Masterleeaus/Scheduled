<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 $prefix='Titan\\BookingSidecar\\';if(str_starts_with($class,$prefix)){$name=substr($class,strlen($prefix));$file=$root.'/private/booking-sidecar/'.$name.'.php';if(is_file($file))require_once $file;}
});
function verify(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function rejected(callable $fn):void{try{$fn();}catch(InvalidArgumentException|DomainException|LogicException $e){return;}throw new RuntimeException('Expected rejection');}

$c=['customer'=>['name'=>'A','email'=>'a@b.test','phone'=>'1234567'],'staff_ids'=>[1],'service'=>['service_id'=>1,'method_id'=>1,'method_unit_id'=>1],'duration_minutes'=>721];rejected(fn()=>Titan\BookingSidecar\BookingLiveWriteCommandPolicy::assert($c));
echo "PASS 15_duration_validation.php\n";
