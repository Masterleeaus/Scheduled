<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Check requested local start against the existing Cleanto schedule under staff locks. */
final class BookingFinalAvailability {
 public function __construct(private \PDO $db){}
 public function assertAvailable(array $command,?int $excludeBooking=null):void {
  $window=$command['window']; $date=substr($window['local_start'],0,10);
  BookingTemporalConsistency::assertWindow($window,$command['timezone']);
  $local=BookingLocalTimeResolution::resolve($window['local_start'],$command['timezone']);
  $requested=$local->getTimestamp();
  foreach($command['staff_ids'] as $staff){
   $query=['staff_id'=>$staff,'date'=>$date,'timezone'=>$command['timezone'],'duration_minutes'=>$command['duration_minutes'],'step_minutes'=>5];
   // The legacy occupied record must be excluded for reschedules; no blanket ignore.
   if($excludeBooking===null){
    $result=(new AvailabilityReader($this->db))->slots($query);
    if(!in_array($requested,$result['slots'],true))throw new \DomainException('Requested time unavailable');
   }else{
    $reader=new ScheduleReader($this->db);
    $from=$date.' 00:00:00';$until=(new \DateTimeImmutable($from,new \DateTimeZone($command['timezone'])))->modify('+1 day')->format('Y-m-d H:i:s');
    $busy=BookingRescheduleConflictFilter::withoutSelf((new BookingConflictReader($this->db))->busy($staff,$from,$until,$command['timezone']),$excludeBooking);
    $slots=AvailabilityEngine::calculate($query,$reader->shifts($staff),$reader->breaks($staff),$reader->timeOff($staff,$from,$until),$reader->daysOff($staff,$date),$busy);
    if(!in_array($requested,$slots,true))throw new \DomainException('Requested reschedule time unavailable');
   }
  }
 }
}
