<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$day=Interval::parse('2026-11-08 00:00:00');$a=$day->getTimestamp()+9*3600;$slots=SlotGrid::generate([[$a,$a+3600]],$day,30,15);t(count($slots)===3,'three half-hour candidates');t(SlotGrid::generate([[$a,$a+1800]],$day,60,15)===[],'long service rejected');
echo "ASSERTIONS $n\n";
