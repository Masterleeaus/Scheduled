<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$d=Interval::parse('2026-11-08 10:00:00');t(ScheduleCalendar::keys($d)['weekday_id']===1,'Sunday index'); t(ScheduleCalendar::keys($d)['week_id']===2,'second month week');t(ScheduleCalendar::matches(['week_id'=>1,'weekday_id'=>1,'provider_schedule_type'=>'weekly'],$d),'weekly key');t(!ScheduleCalendar::matches(['week_id'=>1,'weekday_id'=>1,'provider_schedule_type'=>'monthly'],$d),'monthly mismatch');
echo "ASSERTIONS $n\n";
