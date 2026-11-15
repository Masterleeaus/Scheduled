<?php
declare(strict_types=1);
$base = __DIR__.'/core/app/Http/Controllers/Front/';
$tests = [
 ['BookingPublicSubmissionController.php', 'catch (\\InvalidArgumentException $e)', 'submission rejects malformed input'],
 ['BookingPublicSubmissionController.php', "'Invalid reservation details'", 'submission validation text'],
 ['BookingPublicSubmissionController.php', "'Reservation service unavailable'", 'submission infrastructure failure'],
 ['BookingPublicSubmissionController.php', 'CheckoutSubmissionGate::dispatch', 'submission native executor path'],
 ['BookingPublicSubmissionController.php', "['replay'] === true ? 200 : 201", 'submission replay status'],
 ['BookingPublicSubmissionController.php', "'confirmed'=>false],503", 'submission fail closed'],
 ['BookingPublicCheckoutController.php', 'catch(\\InvalidArgumentException $e)', 'validation invalid inputs'],
 ['BookingPublicCheckoutController.php', "'Booking service unavailable'", 'validation infrastructure status'],
 ['BookingPublicCheckoutController.php', 'CheckoutCommitCompatibility::assert', 'native storage contracts'],
 ['BookingPublicCheckoutController.php', "'Cache-Control','private, no-store'", 'private response headers'],
];
foreach($tests as [$f,$needle,$desc]) {
 $s=file_get_contents($base.$f);
 if($s===false||strpos($s,$needle)===false) {fwrite(STDERR,"FAIL $desc\n");exit(1);}
 echo "PASS $desc\n";
}
