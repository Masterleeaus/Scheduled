<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$p=['staff_id'=>1,'date'=>'2026-11-08','timezone'=>'UTC','duration_minutes'=>60,'step_minutes'=>30];$shifts=[['week_id'=>1,'weekday_id'=>1,'provider_schedule_type'=>'weekly','off_day'=>'N','day_start_time'=>'09:00:00','day_end_time'=>'12:00:00']];$breaks=[['week_id'=>1,'weekday_id'=>1,'break_start'=>'10:00:00','break_end'=>'10:30:00']];$out=AvailabilityEngine::calculate($p,$shifts,$breaks,[],[],[]);t(count($out)===3,'break excludes crossing slots');t(AvailabilityEngine::calculate($p,$shifts,$breaks,[],[['off_date'=>'2026-11-08']],[])===[],'day off blocks all');$busy=[['start'=>'2026-11-08 11:00:00','duration_minutes'=>60]];t(count(AvailabilityEngine::calculate($p,$shifts,[],[],[],$busy))===3,'busy appointment excludes overlaps');
echo "ASSERTIONS $n\n";
