<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRate,LifecycleDurationEvidence,LifecycleOwnershipEvidence,LifecycleCustomerEvidence,LifecycleServiceEvidence,LifecycleCancellationStatus,LifecycleClientOrderRow,LifecycleFullBookingRow,LifecycleReplayOrderEvidence};
try{LifecycleFullBookingRow::parse(['id'=>1,'order_id'=>2,'client_id'=>3,'service_id'=>4,'method_id'=>5,'method_unit_id'=>6,'method_unit_qty'=>1,'method_unit_qty_rate'=>'2.5','staff_ids'=>'','booking_date_time'=>'2027-01-01 10:00:00','booking_status'=>'A']);throw new \RuntimeException('expected rejection');}catch(\Throwable $e){if($e->getMessage()==='expected rejection')throw $e;}
echo 'PASS pass26_full_booking_invalid'.PHP_EOL;
