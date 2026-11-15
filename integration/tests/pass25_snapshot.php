<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecyclePersistedRow,LifecycleCreateEvidence,LifecycleRescheduleEvidence,LifecycleCancellationEvidence,LifecycleReplayEvidence,LifecycleResultIdentity,LifecycleOrderIdentity,LifecycleWindowEvidence,LifecycleEvidenceSnapshot,LifecycleMutationOrder};
function testRow():array{return ['id'=>7,'order_id'=>11,'service_id'=>3,'staff_ids'=>[1,2],'booking_date_time'=>'2027-01-10 09:00:00','booking_status'=>'A'];}
function mustFail(callable $f):void{try{$f();}catch(\Throwable $e){return;}throw new \RuntimeException('Expected rejection');}
$e=LifecycleEvidenceSnapshot::from(testRow());if($e['staff_count']!==2||array_key_exists('email',$e))throw new \RuntimeException('Unsafe snapshot');
echo "PASS pass25_snapshot.php\n";
