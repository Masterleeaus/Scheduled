<?php
declare(strict_types=1);
require __DIR__.'/../../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationTimeGuard,BookingRescheduleFuturePolicy};
function check84(bool $condition,string $description):void {if(!$condition)throw new RuntimeException($description);}
function reject84(callable $fn,string $description):void {try{$fn();}catch(DomainException $e){return;}throw new RuntimeException('Expected rejection: '.$description);}
$now=1700000000;
BookingMutationTimeGuard::assertFuture(['start'=>$now+30],$now);
reject84(fn()=>BookingMutationTimeGuard::assertFuture(['start'=>$now],$now),'booking now');
reject84(fn()=>BookingMutationTimeGuard::assertFuture(['start'=>$now-1],$now),'booking past');
reject84(fn()=>BookingMutationTimeGuard::assertFuture(['start'=>$now+59],$now,1),'booking lead time');
BookingRescheduleFuturePolicy::assertFuture(['start'=>$now+60,'end'=>$now+3600],$now);
reject84(fn()=>BookingRescheduleFuturePolicy::assertFuture(['start'=>$now,'end'=>$now+3600],$now),'reschedule now');
reject84(fn()=>BookingRescheduleFuturePolicy::assertFuture(['start'=>$now-5,'end'=>$now+3600],$now),'reschedule past');
reject84(fn()=>BookingRescheduleFuturePolicy::assertFuture(['start'=>$now+60,'end'=>$now+60],$now),'invalid duration');
$root=realpath(__DIR__.'/../../../private/booking-sidecar');
$creation=file_get_contents($root.'/BookingWriteCoordinator.php');
$reschedule=file_get_contents($root.'/BookingRescheduleCoordinator.php');
check84($creation!==false&&$reschedule!==false,'source not found');
$locs=[];
foreach(['initial'=>'BookingMutationTimeGuard::assertFuture($cmd[\'window\'],time());','pending'=>"if(\$decision==='pending')",'claim'=>'$ledger->claim(', 'availability'=>'(new BookingConflictGuard($this->db))->assertFree($cmd);','write'=>'(new BookingLegacyClientWriter($this->db))->insert'] as $label=>$needle){$locs[$label]=strpos($creation,$needle);check84($locs[$label]!==false,'creation missing '.$label);}
check84(substr_count($creation,'BookingMutationTimeGuard::assertFuture($cmd[\'window\'],time());')===3,'creation requires three time checks');
check84($locs['initial']<$locs['pending']&&$locs['pending']<$locs['claim']&&$locs['claim']<$locs['availability']&&$locs['availability']<$locs['write'],'creation guard ordering');
$first=strpos($creation,'BookingMutationTimeGuard::assertFuture($cmd[\'window\'],time());',$locs['pending']);
$second=strpos($creation,'BookingMutationTimeGuard::assertFuture($cmd[\'window\'],time());',$locs['availability']);
check84($first!==false&&$first<$locs['claim']&&$second!==false&&$second<$locs['write'],'creation late guards');
check84(substr_count($reschedule,'BookingRescheduleFuturePolicy::assertFuture($cmd[\'window\'],time());')===3,'reschedule requires three time checks');
$read=strpos($reschedule,'$current=(new BookingMutationReader');$state=strpos($reschedule,'BookingRescheduleState::assertEligible');$availability=strpos($reschedule,'(new BookingFinalAvailability($this->db))->assertAvailable');$write=strpos($reschedule,'(new BookingRescheduleWriter($this->db))->update');
$afterState=strpos($reschedule,'BookingRescheduleFuturePolicy::assertFuture($cmd[\'window\'],time());',$state);$afterAvailability=strpos($reschedule,'BookingRescheduleFuturePolicy::assertFuture($cmd[\'window\'],time());',$availability);
check84($read!==false&&$read<$state&&$afterState!==false&&$afterState<$availability,'reschedule post-lock guard placement');
check84($afterAvailability!==false&&$afterAvailability<$write,'reschedule pre-write guard placement');
echo "PASS pass84: 14 assertions, post-lock future checks and replay-safe ordering\n";
