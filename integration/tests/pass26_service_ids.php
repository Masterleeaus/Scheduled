<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
if(!(LifecycleServiceEvidence::verify(['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'method_unit_qty'=>4],['service'=>['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'method_unit_qty'=>4]])===null))throw new \RuntimeException('failed assertion');
echo 'PASS pass26_service_ids'.PHP_EOL;
