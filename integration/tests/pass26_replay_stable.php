<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
if(!(LifecycleReplayOrderEvidence::verify(['id'=>1,'order_id'=>2,'rate'=>100],['id'=>1,'order_id'=>2,'rate'=>100])===null))throw new \RuntimeException('failed assertion');
echo 'PASS pass26_replay_stable'.PHP_EOL;
