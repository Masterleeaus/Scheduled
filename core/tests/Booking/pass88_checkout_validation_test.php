<?php
declare(strict_types=1);
require __DIR__.'/../../app/Booking/Public/Checkout/CheckoutCustomer.php';
require __DIR__.'/../../app/Booking/Public/Checkout/CheckoutRequest.php';
use App\Booking\Public\Checkout\CheckoutCustomer;
use App\Booking\Public\Checkout\CheckoutRequest;
$valid=['first_name'=>' Ada ','last_name'=>'Lovelace','email'=>'ADA@EXAMPLE.COM','phone'=>'+61 412 345 678','address'=>'1 Example St','notes'=>'Gate code at front','consent'=>true];
assert(CheckoutCustomer::parse($valid)['email']==='ada@example.com');
assert(CheckoutRequest::parse(['intent_token'=>'abc.def','customer'=>$valid])['customer']['first_name']==='Ada');
$cases=[['consent'=>false],['email'=>'bad'],['phone'=>'invalid'],['first_name'=>''],['address'=>str_repeat('x',500)],['unknown'=>'x'],['notes'=>[1]],['first_name'=>"\xFF"]];
foreach($cases as $case){$bad=array_replace($valid,$case);try{CheckoutCustomer::parse($bad);throw new RuntimeException('Expected validation rejection');}catch(InvalidArgumentException $e){}}
foreach([['intent_token'=>'x','customer'=>[],'price'=>10],['intent_token'=>[], 'customer'=>$valid]] as $bad){try{CheckoutRequest::parse($bad);throw new RuntimeException('Expected validation rejection');}catch(InvalidArgumentException $e){}}
echo "Pass 88 checkout validation: 12 cases passed\n";
