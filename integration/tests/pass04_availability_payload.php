<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$p=AvailabilityPayload::parse(['staff_id'=>2,'duration_minutes'=>60,'date'=>'2026-11-10','timezone'=>'Australia/Melbourne']); t($p['step_minutes']===15,'default step'); reject(fn()=>AvailabilityPayload::parse(['staff_id'=>2,'duration_minutes'=>0,'date'=>'2026-11-10']),'duration rejected'); reject(fn()=>AvailabilityPayload::parse(['staff_id'=>2,'duration_minutes'=>60,'date'=>'2026-02-30']),'invalid day');
echo "ASSERTIONS $n\n";
