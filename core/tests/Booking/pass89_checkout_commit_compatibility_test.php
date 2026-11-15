<?php
declare(strict_types=1);
$root=dirname(__DIR__,2).'/app';
foreach ([
 'Booking/Public/Redemption/RedeemSelection.php',
 'Booking/Public/Redemption/CommitSelection.php',
 'Booking/Public/Redemption/CommitLegacyCustomer.php',
 'Booking/Public/Redemption/CommitCustomerIdentityPolicy.php',
 'Booking/Public/Redemption/CommitLegacyClaimKey.php',
 'Booking/Public/Redemption/CommitLegacySelection.php',
 'Booking/Public/Checkout/CheckoutCustomer.php',
 'Booking/Public/Checkout/CheckoutNativeCustomer.php',
 'Booking/Public/Checkout/CheckoutCommitCompatibility.php'
] as $part) require_once $root.'/'.$part;
use App\Booking\Public\Checkout\CheckoutCommitCompatibility;
$selection=['service_id'=>1,'method_id'=>1,'unit_id'=>1,'staff_id'=>1,'quantity'=>1,'date_time'=>'2030-12-10 10:00:00','duration_minutes'=>60,'timezone'=>'Australia/Melbourne'];
$customer=['first_name'=>'Jane','last_name'=>'Smith','email'=>'jane@example.test','phone'=>'0412345678','address'=>'42 Example Road','notes'=>'Gate code in booking','consent'=>true];
$nonce='0123456789abcdef0123456789abcdef';
$passed=0;
function check(array $s,array $c,string $nonce,bool $shouldPass):void {global $passed;try{CheckoutCommitCompatibility::assert($s,$c,$nonce); if(!$shouldPass)throw new RuntimeException('Invalid candidate was accepted');}catch(InvalidArgumentException $e){if($shouldPass)throw $e;} $passed++;}
check($selection,$customer,$nonce,true);
check($selection,array_merge($customer,['phone'=>'+61 412 345 678 90']),$nonce,false);
check($selection,array_merge($customer,['email'=>str_repeat('a',195).'@x.test']),$nonce,false);
check($selection,array_merge($customer,['first_name'=>str_repeat('a',80),'last_name'=>str_repeat('b',80)]),$nonce,false);
check($selection,array_merge($customer,['phone'=>"0412\n345678"]),$nonce,false);
check(array_merge($selection,['duration_minutes'=>900]),$customer,$nonce,false);
check(array_merge($selection,['staff_id'=>0]),$customer,$nonce,false);
check($selection,$customer,'bad',false);
echo "Pass89 checkout/native persistence compatibility: $passed/8 passed\n";
