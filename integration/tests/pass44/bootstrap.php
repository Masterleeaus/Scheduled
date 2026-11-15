<?php
error_reporting(E_ALL);
$root=dirname(__DIR__,3).'/private/booking-sidecar/';
spl_autoload_register(static function(string $class)use($root):void { $prefix='Titan\\BookingSidecar\\';if(str_starts_with($class,$prefix)){$path=$root.substr($class,strlen($prefix)).'.php';if(is_file($path))require_once $path;}});
function pass44_expect(callable $f,bool $throws=false):void {try{$f();if($throws)throw new RuntimeException('Expected rejection');}catch(DomainException|InvalidArgumentException|LogicException $e){if(!$throws)throw $e;}}
function pass44_booking():array{return ['id'=>'17','order_id'=>'29','client_id'=>'31','booking_date_time'=>'2026-11-11 10:00:00','service_id'=>'2','method_id'=>'3','method_unit_id'=>'4','method_unit_qty'=>'2','method_unit_qty_rate'=>'50.00','booking_status'=>'A','staff_ids'=>'7'];}
function pass44_order():array{return ['id'=>'31','order_id'=>'29','client_name'=>'Jane Example','client_email'=>'jane@example.org','client_phone'=>'0400000000','order_duration'=>'120'];}
function pass44_command():array{return ['service'=>['service_id'=>2,'method_id'=>3,'method_unit_id'=>4,'quantity'=>2],'staff_ids'=>[7],'duration_minutes'=>120,'window'=>['local_start'=>'2026-11-11 10:00:00'],'customer'=>['name'=>'Jane Example','email'=>'jane@example.org','phone'=>'0400000000']];}
