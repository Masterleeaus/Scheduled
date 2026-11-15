<?php
declare(strict_types=1);
require_once __DIR__.'/../../app/Booking/Public/Checkout/CheckoutCustomer.php';
use App\Booking\Public\Checkout\CheckoutCustomer;
$base = [
 'first_name'=>'Taylor','last_name'=>'Example','email'=>'Taylor@example.test',
 'address'=>'1 Test Street','notes'=>'Call first','consent'=>true,
];
$cases=[
 ['(03) 9123 4567','0391234567'],
 ['+61 (0) 412 345 678','+610412345678'],
 ['+1 (415) 555-2671','+14155552671'],
 ['0412 345 678','0412345678'],
 ['1234567','1234567'],
 ['+12345678901234','+12345678901234'],
];
$count=0;
foreach($cases as [$input,$expected]) {
 $actual=CheckoutCustomer::parse($base+['phone'=>$input])['phone'];
 if($actual!==$expected) throw new RuntimeException("Phone $input produced $actual rather than $expected");
 if(strlen($actual)>15) throw new RuntimeException('Native column overflow');
 $count++;
}
$bad=['+123456789012345','1234567890123456','123456','+1+23456789','123\n456789','+1234567890123456789','+1-222-ABC-555','+123456789012345 (ext 2)'];
foreach($bad as $input) {
 try {CheckoutCustomer::parse($base+['phone'=>$input]);throw new RuntimeException('Accepted invalid number: '.$input);} 
 catch(InvalidArgumentException $e) {} 
 $count++;
}
$raw=CheckoutCustomer::parse($base+['phone'=>'+61 (0) 412 345 678']);
require_once __DIR__.'/../../app/Booking/Public/Redemption/CommitLegacyCustomer.php';
require_once __DIR__.'/../../app/Booking/Public/Redemption/CommitCustomerIdentityPolicy.php';
use App\Booking\Public\Redemption\CommitCustomerIdentityPolicy;
$native=CommitCustomerIdentityPolicy::requireSafe(['name'=>$raw['first_name'].' '.$raw['last_name'],'email'=>$raw['email'],'phone'=>$raw['phone']]);
if($native['phone']!=='+610412345678') throw new RuntimeException('Native handoff changed phone');
$count++;
echo "Pass 99 phone boundary: $count assertions passed\n";
