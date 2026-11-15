<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{AvailabilityPayload,ScheduleCalendar,Interval,IntervalSet,TimeOfDay,StaffShiftWindow,BreakWindow,TimeOffWindow,DayOffPolicy,SlotGrid,BookingBusyWindow,AvailabilityEngine};
$n=0;
function t(bool $ok,string $message):void {global $n; if(!$ok)throw new RuntimeException('FAIL: '.$message);$n++;echo 'PASS '.$message."\n";}
function reject(callable $f,string $message):void {try{$f();throw new RuntimeException('Not rejected');}catch(InvalidArgumentException $e){t(true,$message);}}
$x=file_get_contents(dirname(__DIR__,2).'/core/app/Booking/InternalBookingClient.php');t(str_contains($x,"'availability'"),'Laravel operation allowlisted');$d=file_get_contents(dirname(__DIR__,2).'/private/booking-sidecar/RequestHandler.php');t(str_contains($d,"'availability'"),'private endpoint allowlisted');t(!str_contains($d,"'create_booking'"),'write operation remains disabled');
echo "ASSERTIONS $n\n";
