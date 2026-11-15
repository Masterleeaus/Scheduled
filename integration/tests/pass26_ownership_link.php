<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
if(!(LifecycleOwnershipEvidence::verify(['order_id'=>8,'client_id'=>3],['order_id'=>8,'id'=>3])===null))throw new \RuntimeException('failed assertion');
echo 'PASS pass26_ownership_link'.PHP_EOL;
