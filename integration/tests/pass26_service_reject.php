<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
try{LifecycleServiceEvidence::verify(['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'method_unit_qty'=>4],['service'=>['service_id'=>1,'method_id'=>9,'method_unit_id'=>3,'method_unit_qty'=>4]]);throw new \RuntimeException('expected rejection');}catch(\Throwable $e){if($e->getMessage()==='expected rejection')throw $e;}
echo 'PASS pass26_service_reject'.PHP_EOL;
