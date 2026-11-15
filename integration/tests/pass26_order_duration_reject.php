<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
try{LifecycleDurationEvidence::verify(['order_duration'=>90],['duration_minutes'=>60]);throw new \RuntimeException('expected rejection');}catch(\Throwable $e){if($e->getMessage()==='expected rejection')throw $e;}
echo 'PASS pass26_order_duration_reject'.PHP_EOL;
