<?php
declare(strict_types=1);
$dir=dirname(__DIR__,3).'/core/app/Booking/Public/Redemption/';
foreach(['CommitBookingId','CommitOrderDuration','CommitOrderRow','CommitCustomerOrder'] as $f)require_once $dir.$f.'.php';
$c='App\Booking\Public\Redemption\CommitCustomerOrder';
try{$c::validate(['order_id'=>2],['order_id'=>3,'client_name'=>'N','client_email'=>'n@x.tld','client_phone'=>'123']);exit(1);}catch(RuntimeException $e){}
echo "PASS order_mismatch_behavior
";
