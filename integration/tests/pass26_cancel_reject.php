<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
try{LifecycleCancellationStatus::verify(['id'=>1,'order_id'=>2,'service_id'=>3,'booking_status'=>'RS'],['id'=>1,'order_id'=>2,'service_id'=>3,'booking_status'=>'MN']);throw new \RuntimeException('expected rejection');}catch(\Throwable $e){if($e->getMessage()==='expected rejection')throw $e;}
echo 'PASS pass26_cancel_reject'.PHP_EOL;
