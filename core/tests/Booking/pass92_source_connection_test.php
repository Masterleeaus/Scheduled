<?php
 declare(strict_types=1);
 $root=dirname(__DIR__,2).'/app/Booking/Public';
 $checks=[
 ['Redemption/CommitCleantoNativeGateway.php','array $personalInfo=[]'],
 ['Redemption/CommitCleantoNativeGateway.php','$writer,$fresh,$locks,$personalInfo'],
 ['Redemption/CommitLegacyGateway.php','CommitLegacyAdapter::writer($persist,$key,$personalInfo)'],
 ['Redemption/CommitLegacyAdapter.php',"\$cmd['personal_info']="],
 ['Checkout/CheckoutCommitCompatibility.php','CheckoutNativeCustomer::map($customer)'],
 ['Checkout/CheckoutNativeCustomer.php','BookingPersonalInfo::parse'],
 ];
 foreach($checks as [$path,$needle]){
   $source=file_get_contents($root.'/'.$path);
   if($source===false||strpos($source,$needle)===false)throw new RuntimeException('Missing native handoff: '.$path.' '.$needle);
 }
 echo 'Pass92 native source handoff: '.count($checks).' assertions passed'.PHP_EOL;
