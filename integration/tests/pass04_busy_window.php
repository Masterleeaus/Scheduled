<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$r=[['start'=>'2026-11-08 10:00:00','duration_minutes'=>90]];$busy=BookingBusyWindow::fromRecords($r,'UTC');t($busy[0][1]-$busy[0][0]===5400,'duration preserved');try{BookingBusyWindow::fromRecords([['start'=>'2026-11-08 10:00:00','duration_minutes'=>0]],'UTC');throw new Exception('should fail');}catch(RuntimeException|DomainException $e){t(true,'missing duration fails closed');}
echo "ASSERTIONS $n\n";
