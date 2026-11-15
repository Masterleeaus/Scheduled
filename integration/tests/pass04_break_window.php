<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$day=Interval::parse('2026-11-08 00:00:00');$s=[['week_id'=>1,'weekday_id'=>1,'provider_schedule_type'=>'weekly']];$b=[['week_id'=>1,'weekday_id'=>1,'break_start'=>'12:00:00','break_end'=>'12:30:00']];$w=BreakWindow::forDay($b,$day,'UTC',$s);t(count($w)===1 && $w[0][1]-$w[0][0]===1800,'half hour break');t(BreakWindow::forDay($b,$day,'UTC',[])===[],'unmatched break ignored');
echo "ASSERTIONS $n\n";
