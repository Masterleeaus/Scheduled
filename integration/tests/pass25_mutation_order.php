<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRow,LifecycleCreateEvidence,LifecycleRescheduleEvidence,LifecycleCancellationEvidence,LifecycleReplayEvidence,LifecycleResultIdentity,LifecycleOrderIdentity,LifecycleWindowEvidence,LifecycleEvidenceSnapshot,LifecycleMutationOrder};
function testRow():array{return ['id'=>7,'order_id'=>11,'service_id'=>3,'staff_ids'=>[1,2],'booking_date_time'=>'2027-01-10 09:00:00','booking_status'=>'A'];}
function mustFail(callable $f):void{try{$f();}catch(\Throwable $e){return;}throw new \RuntimeException('Expected rejection');}
LifecycleMutationOrder::assert(['real_insert'=>true,'idempotency'=>true,'sequential_overlap'=>true,'reschedule'=>true,'cancel'=>true]);mustFail(fn()=>LifecycleMutationOrder::assert(['real_insert'=>true]));
echo "PASS pass25_mutation_order.php\n";
