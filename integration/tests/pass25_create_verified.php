<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRow,LifecycleCreateEvidence,LifecycleRescheduleEvidence,LifecycleCancellationEvidence,LifecycleReplayEvidence,LifecycleResultIdentity,LifecycleOrderIdentity,LifecycleWindowEvidence,LifecycleEvidenceSnapshot,LifecycleMutationOrder};
function testRow():array{return ['id'=>7,'order_id'=>11,'service_id'=>3,'staff_ids'=>[1,2],'booking_date_time'=>'2027-01-10 09:00:00','booking_status'=>'A'];}
function mustFail(callable $f):void{try{$f();}catch(\Throwable $e){return;}throw new \RuntimeException('Expected rejection');}
$f=['create'=>['service'=>['service_id'=>3],'staff_ids'=>[2,1],'start_at'=>'2027-01-10 09:00:00']];LifecycleCreateEvidence::verify(testRow(),7,$f);
echo "PASS pass25_create_verified.php\n";
