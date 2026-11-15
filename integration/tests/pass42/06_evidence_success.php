<?php
declare(strict_types=1);
$root=dirname(__DIR__,3);
spl_autoload_register(static function(string $class)use($root):void {
 $prefix='Titan\\BookingSidecar\\';if(str_starts_with($class,$prefix)){$name=substr($class,strlen($prefix));$file=$root.'/private/booking-sidecar/'.$name.'.php';if(is_file($file))require_once $file;}
});
function verify(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function rejected(callable $fn):void{try{$fn();}catch(InvalidArgumentException|DomainException|LogicException $e){return;}throw new RuntimeException('Expected rejection');}

$c=Titan\BookingSidecar\BookingLiveWriteColumns::required();$e=array_fill_keys(array_keys($c),'InnoDB');$r=Titan\BookingSidecar\BookingLiveWriteEvidence::assess(['columns'=>$c,'engines'=>$e]);verify($r['ready']===true&&$r['errors']===[],'ready');
echo "PASS 06_evidence_success.php\n";
