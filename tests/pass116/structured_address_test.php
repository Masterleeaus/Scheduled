<?php
declare(strict_types=1);
$root=dirname(__DIR__,2);
require_once $root.'/private/booking-sidecar/BookingPersonalInfo.php';
require_once $root.'/core/app/Booking/Public/Redemption/CommitLegacyCustomer.php';
require_once $root.'/core/app/Booking/Public/Redemption/CommitCustomerIdentityPolicy.php';
require_once $root.'/core/app/Booking/Public/Checkout/CheckoutCustomer.php';
require_once $root.'/core/app/Booking/Public/Checkout/CheckoutNativeCustomer.php';
use App\Booking\Public\Checkout\CheckoutCustomer;
use App\Booking\Public\Checkout\CheckoutNativeCustomer;
$n=0;
function must(bool $ok,string $reason):void {global $n;if(!$ok)throw new RuntimeException($reason);$n++;}
function deny(array $input,string $reason):void {global $n;try {CheckoutNativeCustomer::map($input);}catch(InvalidArgumentException $e){$n++;return;}throw new RuntimeException($reason);}
$raw=['first_name'=>'Lena','last_name'=>'Nguyen','email'=>'LENA@example.com','phone'=>'+61 412 345 678','address'=>'21 Harbour St','city'=>'Melbourne','state'=>'Victoria','zip'=>'3000','notes'=>'Intercom 14','consent'=>true];
$parsed=CheckoutCustomer::parse($raw);
$mapped=CheckoutNativeCustomer::map($raw);
foreach(['city'=>'Melbourne','state'=>'Victoria','zip'=>'3000','address'=>'21 Harbour St','notes'=>'Intercom 14'] as $field=>$expected){must($mapped['personal_info'][$field]===$expected,'missing '.$field);}
must($mapped['identity']['email']==='lena@example.com','email lowercase');
$encoded=\Titan\BookingSidecar\BookingPersonalInfo::encode($mapped['personal_info']);
$stored=unserialize(base64_decode($encoded),['allowed_classes'=>false]);
foreach(['city','state','zip','address','notes'] as $field)must($stored[$field]===$mapped['personal_info'][$field],'serialized '.$field);
must(count($stored)===8,'Cleanto native eight-field format');
$legacy=$raw;unset($legacy['city'],$legacy['state'],$legacy['zip']);
$old=CheckoutNativeCustomer::map($legacy);
foreach(['city','state','zip'] as $field)must($old['personal_info'][$field]==='','legacy '.$field);
foreach(['city'=>str_repeat('c',101),'state'=>str_repeat('s',101),'zip'=>str_repeat('9',25),'email'=>str_repeat('a',191).'@example.com'] as $key=>$bad)deny(array_replace($raw,[$key=>$bad]),'reject '.$key);
deny(array_replace($raw,['zip'=>"\x00oops"]),'zip controls');
foreach (['city','state','zip'] as $key)deny(array_replace($raw,[$key=>['bad']]),'type '.$key);
deny(array_replace($raw,['tenant_id'=>77]),'forged authority');
echo "Pass116 structured address: $n assertions passed\n";
