<?php
declare(strict_types=1);
$root=dirname(__DIR__,2);
$checks=[
 'booking submission disabled unless explicit enabled'=>['app/Http/Controllers/Front/BookingPublicSubmissionController.php',"config('booking_internal.public_commit_enabled', false) !== true"],
 'booking requires registered executor'=>['app/Http/Controllers/Front/BookingPublicSubmissionController.php',"app()->bound(CheckoutSubmissionExecutor::class)"],
 'booking MariaDB driver checked'=>['app/Http/Controllers/Front/BookingPublicSubmissionController.php',"extension_loaded('pdo_mysql')"],
 'booking tenant eligibility checked'=>['app/Http/Controllers/Front/BookingPublicSubmissionController.php','CheckoutTenantReadiness::eligible'],
 'payment settlement not inferred from event'=>['app/Http/Controllers/Front/BookingPublicCheckoutController.php',"'payment_settled'=>false"],
];
$fail=[];
foreach($checks as $name=>[$file,$needle]){
 $body=@file_get_contents($root.'/'.$file);
 if($body===false||!str_contains($body,$needle))$fail[]=$name;
}
if($fail){fwrite(STDERR,json_encode(['result'=>'FAIL','errors'=>$fail])."\n");exit(1);}
echo json_encode(['result'=>'PASS','checks'=>count($checks)])."\n";
