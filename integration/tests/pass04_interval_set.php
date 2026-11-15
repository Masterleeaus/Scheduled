<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
t(IntervalSet::merge([[10,20],[20,30],[5,8]])===[[5,8],[10,30]],'merge adjacent');t(IntervalSet::subtract([[10,40]],[[15,20],[25,50]])===[[10,15],[20,25]],'subtract multiple');t(IntervalSet::subtract([[10,20]],[[0,30]])===[],'fully blocked');
echo "ASSERTIONS $n\n";
