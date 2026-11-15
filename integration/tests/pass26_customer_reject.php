<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
try{LifecycleCustomerEvidence::verify(['email_fingerprint'=>hash('sha256','a@example.org')],['customer'=>['email'=>'b@example.org']]);throw new \RuntimeException('expected rejection');}catch(\Throwable $e){if($e->getMessage()==='expected rejection')throw $e;}
echo 'PASS pass26_customer_reject'.PHP_EOL;
