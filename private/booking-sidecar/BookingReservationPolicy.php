<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use DomainException;
/** Fails closed on schedule gaps, active conflicts and absent staff snapshots. */
final class BookingReservationPolicy {
 public static function verify(array $command,array $staffFree,array $staffBusy):void {
  $window=$command['window'];foreach($command['staff_ids'] as $id){
   if(!array_key_exists($id,$staffFree)||!BookingSchedulePolicy::permits($window,[$staffFree[$id]]))throw new DomainException('Staff unavailable');
   if(!array_key_exists($id,$staffBusy))throw new DomainException('Staff conflicts not checked');
   foreach($staffBusy[$id] as $busy)if(BookingTimeWindow::overlaps($window,$busy))throw new DomainException('Booking conflict');
  }
 }
}
