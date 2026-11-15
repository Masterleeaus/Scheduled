<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$rows=[['start_date_time'=>'2026-11-08 10:00:00','end_date_time'=>'2026-11-08 12:00:00']];$x=TimeOffWindow::fromRows($rows,'UTC');t(count($x)===1 && $x[0][1]-$x[0][0]===7200,'offtime duration');t(DayOffPolicy::blocked([['status'=>1]]),'unknown status blocks safely');t(!DayOffPolicy::blocked([]),'no off row');
echo "ASSERTIONS $n\n";
