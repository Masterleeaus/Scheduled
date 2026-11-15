<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 $prefix='Titan\\BookingSidecar\\';if(str_starts_with($class,$prefix)){$name=substr($class,strlen($prefix));$file=$root.'/private/booking-sidecar/'.$name.'.php';if(is_file($file))require_once $file;}
});
function verify(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function rejected(callable $fn):void{try{$fn();}catch(InvalidArgumentException|DomainException|LogicException $e){return;}throw new RuntimeException('Expected rejection');}

$code=file_get_contents($root.'/private/booking-sidecar/BookingLiveWriteSnapshot.php');verify(substr_count($code,'TABLE_SCHEMA=DATABASE()')===2,'tenant database scope');verify(str_contains($code,'->prepare('),'parameterized metadata');
echo "PASS 20_schema_reader_scoped.php\n";
