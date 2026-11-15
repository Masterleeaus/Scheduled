<?php
declare(strict_types=1);
require_once __DIR__.'/../../private/booking-sidecar/BookingPersonalInfo.php';
require_once __DIR__.'/../../private/booking-sidecar/BookingWriteProofPersonalInfo.php';
use Titan\BookingSidecar\BookingPersonalInfo;
use Titan\BookingSidecar\BookingWriteProofPersonalInfo;
$checks=0;
function ok(bool $v,string $msg):void {global $checks;if(!$v)throw new RuntimeException($msg);$checks++;}
function rejects($stored,array $expected):void {global $checks;try{BookingWriteProofPersonalInfo::check($stored,$expected);}catch(DomainException $e){$checks++;return;}throw new RuntimeException('Expected mismatch');}
$info=['address'=>'10 Main St','city'=>'Melbourne','state'=>'VIC','zip'=>'3000','notes'=>'Use rear door'];
$encoded=BookingPersonalInfo::encode($info);
BookingWriteProofPersonalInfo::check($encoded,$info);ok(true,'exact location round trip');
BookingWriteProofPersonalInfo::check(BookingPersonalInfo::encode([]),[]);ok(true,'empty location accepted');
rejects(null,$info);rejects('',$info);rejects('not-serialized',$info);
foreach(['address','city','state','zip','notes'] as $key){$changed=$info;$changed[$key].=' X';rejects($encoded,$changed);}
rejects(BookingPersonalInfo::encode([]),$info);
$permuted=array_reverse($info,true);BookingWriteProofPersonalInfo::check($encoded,$permuted);ok(true,'field order independent');
$unicode=['address'=>'12 Rue de l’École','notes'=>'Café'];BookingWriteProofPersonalInfo::check(BookingPersonalInfo::encode($unicode),$unicode);ok(true,'UTF8 supported');
$encodedTampered=substr($encoded,0,-2).'AA';rejects($encodedTampered,$info);
echo "PASS115: {$checks} runtime assertions passed\n";
