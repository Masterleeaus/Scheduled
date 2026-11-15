<?php
declare(strict_types=1);
$root=dirname(__DIR__,2);
$read=static fn(string $name):string=>file_get_contents($root.'/'.$name);
$cases=[
    'command captures validated personal info'=>str_contains($read('private/booking-sidecar/BookingCommand.php'),"BookingPersonalInfo::parse(\$r['personal_info']??[])") ,
    'command carries personal info to writer'=>str_contains($read('private/booking-sidecar/BookingCommand.php'),"'personal_info'=>\$personalInfo"),
    'native persist forwards personal info'=>str_contains($read('private/booking-sidecar/BookingSharedPersist.php'),"\$command['personal_info']??[]"),
    'native client insert uses encoded info'=>str_contains($read('private/booking-sidecar/BookingLegacyClientWriter.php'),"\$encodedInfo,\$duration"),
    'native policy rechecks personal info'=>str_contains($read('private/booking-sidecar/BookingLiveWriteCommandPolicy.php'),"BookingPersonalInfo::parse(\$command['personal_info']??[])"),
    'public checkout validates native representation'=>str_contains($read('core/app/Booking/Public/Checkout/CheckoutCommitCompatibility.php'),'BookingPersonalInfo::encode(['),
    'public checkout does not issue writes'=>!str_contains($read('core/app/Http/Controllers/Front/BookingPublicCheckoutController.php'),'CommitCleantoNativeGateway::execute('),
];
foreach($cases as $label=>$pass)if(!$pass)throw new RuntimeException('FAIL '.$label);
echo 'PASS '.count($cases)." integration assertions\n";
