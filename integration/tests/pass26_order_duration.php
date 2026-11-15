<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
if(!(LifecycleDurationEvidence::verify(['order_duration'=>90],['duration_minutes'=>90])===null))throw new \RuntimeException('failed assertion');
echo 'PASS pass26_order_duration'.PHP_EOL;
