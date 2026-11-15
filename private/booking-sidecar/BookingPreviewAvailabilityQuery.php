<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewAvailabilityQuery {
 public static function forStaff(array $p,int $staffId):array {
  if(!in_array($staffId,$p['staff_ids'],true))throw new \DomainException('Unselected staff');
  return ['date'=>$p['date'],'timezone'=>$p['timezone'],'staff_id'=>$staffId,'duration_minutes'=>$p['duration_minutes'],'step_minutes'=>$p['step_minutes']];
 }
}
