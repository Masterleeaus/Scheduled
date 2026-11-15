<?php
 declare(strict_types=1);
 $core=dirname(__DIR__,2).'/app/Booking/Public';
 $sidecar=dirname(__DIR__,3).'/private/booking-sidecar';
 foreach (['BookingPersonalInfo.php'] as $f)require_once $sidecar.'/'.$f;
 foreach (['Redemption/CommitLegacyCustomer.php','Redemption/CommitCustomerIdentityPolicy.php','Checkout/CheckoutCustomer.php','Checkout/CheckoutNativeCustomer.php'] as $f)require_once $core.'/'.$f;
 use App\Booking\Public\Checkout\CheckoutNativeCustomer;
 use Titan\BookingSidecar\BookingPersonalInfo;
 $n=0;
 function ok(bool $v,string $msg):void {global $n;if(!$v)throw new RuntimeException($msg);$n++;}
 function rejected(callable $f,string $msg):void {global $n;try{$f();}catch(InvalidArgumentException $e){$n++;return;}throw new RuntimeException($msg);}
 $input=['first_name'=>'Jane','last_name'=>'Smith','email'=>'JANE@example.com','phone'=>'0412345678','address'=>'42 Example Road','notes'=>'Side entrance','consent'=>true];
 $mapped=CheckoutNativeCustomer::map($input);
 ok($mapped['identity']['name']==='Jane Smith','name');
 ok($mapped['identity']['email']==='jane@example.com','email');
 ok($mapped['identity']['phone']==='0412345678','phone');
 ok($mapped['personal_info']['address']==='42 Example Road','address');
 ok($mapped['personal_info']['notes']==='Side entrance','notes');
 ok(count($mapped)===2,'no authority fields');
 ok(str_contains(base64_decode(BookingPersonalInfo::encode($mapped['personal_info'])),'Side entrance'),'original Cleanto encoding');
 rejected(fn()=>CheckoutNativeCustomer::map(array_merge($input,['consent'=>false])),'consent required');
 rejected(fn()=>CheckoutNativeCustomer::map(array_merge($input,['phone'=>'+61 412 345 678 90'])),'Cleanto phone size');
 rejected(fn()=>CheckoutNativeCustomer::map(array_merge($input,['tenant_id'=>2])),'forbid tenant injection');
 rejected(fn()=>CheckoutNativeCustomer::map(array_merge($input,['address'=>str_repeat('x',251)])),'address bound');
 rejected(fn()=>CheckoutNativeCustomer::map(array_merge($input,['notes'=>str_repeat('x',1001)])),'notes bound');
 echo "Pass92 native checkout data: $n assertions passed\n";
