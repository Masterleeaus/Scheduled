<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/BookingPersonalInfo.php';
use Titan\BookingSidecar\BookingPersonalInfo;
$assertions=0;
function check($condition,string $message):void { global $assertions; if (!$condition) throw new RuntimeException($message); $assertions++; }
function rejects(array $payload):bool { try {BookingPersonalInfo::encode($payload);return false;}catch(InvalidArgumentException $e){return true;} }
$encoded=BookingPersonalInfo::encode(['address'=>'12 Example St, Melbourne','notes'=>'Gate code on arrival']);
$data=unserialize(base64_decode($encoded,true),['allowed_classes'=>false]);
check(is_array($data),'Cleanto array');
check(array_keys($data)===['zip','address','city','state','notes','vc_status','p_status','contact_status'],'Cleanto original eight keys');
check($data['address']==='12 Example St, Melbourne','Address persisted in native structure');
check($data['notes']==='Gate code on arrival','Notes persisted in native structure');
check($data['vc_status']==='' && $data['p_status']==='' && $data['contact_status']==='','No customer-supplied status');
check(rejects(['address'=>'ok','booking_status'=>'confirmed']),'Reject privileged extra fields');
check(rejects(['address'=>str_repeat('A',251)]),'Reject address overflow');
check(rejects(['notes'=>str_repeat('N',1001)]),'Reject notes overflow');
check(rejects(['address'=>"a\0b"]),'Reject NUL');
check(rejects(['address'=>"\xC3\x28"]),'Reject invalid UTF-8');
check(rejects(['address'=>[]]),'Reject array instead of text');
check(BookingPersonalInfo::parse([])['address']==='' && BookingPersonalInfo::parse([])['notes']==='','Legacy callers default to empty');
check(BookingPersonalInfo::parse(['address'=>'   Unit 1   '])['address']==='Unit 1','Trim surrounding whitespace');
check(BookingPersonalInfo::parse(['notes'=>str_repeat('é',1000)])['notes']===str_repeat('é',1000),'Unicode character bounds');
check(rejects(['notes'=>str_repeat('é',1001)]),'Unicode overflow');
check(!str_contains($encoded,'Gate code'),'Encode opaque Cleanto original serialized data');
echo "PASS $assertions booking-personal-info assertions\n";
