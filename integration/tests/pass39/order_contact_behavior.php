<?php
declare(strict_types=1);
$dir=dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/';
foreach(['CommitBookingId','CommitCustomerOrder'] as $f)require_once $dir.$f.'.php';
$c='App\Booking\Public\Redemption\CommitCustomerOrder';
foreach(['client_name','client_email','client_phone'] as $missing){$order=['order_id'=>3,'client_name'=>'N','client_email'=>'n@x.tld','client_phone'=>'123'];$order[$missing]='';try{$c::validate(['order_id'=>3],$order);exit(1);}catch(RuntimeException $e){}}
echo "PASS order_contact_behavior
";
