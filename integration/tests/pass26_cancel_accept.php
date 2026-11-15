<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
if(!(LifecycleCancellationStatus::verify(['id'=>1,'order_id'=>2,'service_id'=>3,'booking_status'=>'RS'],['id'=>1,'order_id'=>2,'service_id'=>3,'booking_status'=>'CC'])===null))throw new \RuntimeException('failed assertion');
echo 'PASS pass26_cancel_accept'.PHP_EOL;
