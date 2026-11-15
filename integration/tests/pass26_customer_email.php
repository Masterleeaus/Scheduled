<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
if(!(LifecycleCustomerEvidence::verify(['email_fingerprint'=>hash('sha256','a@example.org')],['customer'=>['email'=>'A@example.org']])===null))throw new \RuntimeException('failed assertion');
echo 'PASS pass26_customer_email'.PHP_EOL;
