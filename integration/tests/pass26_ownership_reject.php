<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
try{LifecycleOwnershipEvidence::verify(['order_id'=>8,'client_id'=>3],['order_id'=>8,'id'=>4]);throw new \RuntimeException('expected rejection');}catch(\Throwable $e){if($e->getMessage()==='expected rejection')throw $e;}
echo 'PASS pass26_ownership_reject'.PHP_EOL;
