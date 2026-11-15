<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/CleantoStaffAdministration.php';
use Titan\BookingSidecar\CleantoStaffAdministration as S;
function test($v,string $name):void {if(!$v)throw new RuntimeException($name);echo "OK: $name\n";}
test(S::parseIds('')===[],'empty');
test(S::parseIds('1, 2,9')===[1,2,9],'parse native csv');
test(S::modifiedIds([1,3],2,true)===[1,2,3],'assign preserves other services');
test(S::modifiedIds([1,2,3],2,false)===[1,3],'remove preserves other services');
test(S::modifiedIds([1,2,3],2,true)===[1,2,3],'idempotent assignment');
foreach(['1,1','0','-1','1,abc','1,','2147483648',str_repeat('9',2050)] as $raw){try{S::parseIds($raw);throw new RuntimeException('accepted invalid: '.$raw);}catch(DomainException $e){test(true,'reject invalid native ids');}}
foreach([['staff_id'=>'1','service_id'=>1,'assigned'=>true],['staff_id'=>1,'service_id'=>0,'assigned'=>false],['staff_id'=>1,'service_id'=>1,'assigned'=>'true'],['staff_id'=>1,'service_id'=>1,'assigned'=>true,'extra'=>1]] as $bad){try{S::updateInput($bad);throw new RuntimeException('accepted invalid payload');}catch(InvalidArgumentException $e){test(true,'reject malformed payload');}}
test(S::updateInput(['staff_id'=>1,'service_id'=>3,'assigned'=>false])===[1,3,false],'accept strict request');
echo 'Pass31 staff input tests completed.';
