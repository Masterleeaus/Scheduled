<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$day=Interval::parse('2026-11-08 00:00:00');$s=[['week_id'=>1,'weekday_id'=>1,'provider_schedule_type'=>'weekly','off_day'=>'N','day_start_time'=>'09:00:00','day_end_time'=>'17:00:00']];$w=StaffShiftWindow::forDay($s,$day,'UTC');t(count($w)===1 && $w[0][1]-$w[0][0]===28800,'eight hour shift');$s[0]['off_day']='Y';t(StaffShiftWindow::forDay($s,$day,'UTC')===[],'explicit day off');
echo "ASSERTIONS $n\n";
