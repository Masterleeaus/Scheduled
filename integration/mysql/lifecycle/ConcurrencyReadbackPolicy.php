<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyReadbackPolicy {
 public static function assert(array $row,int $winner,array $identity):void {
  if((int)($row['id']??0)!==$winner||(int)($row['service_id']??0)!==$identity['service_id']||($row['booking_date_time']??null)!==$identity['start_at'])throw new \RuntimeException('Committed booking does not match fixture');
  if(!in_array((string)($row['booking_status']??''),['A','C'],true))throw new \RuntimeException('Committed booking has unexpected status');
  $staff=explode(',',(string)($row['staff_ids']??''));$actual=[];foreach($staff as $id){$id=trim($id);if(!ctype_digit($id)||(int)$id<1)throw new \RuntimeException('Invalid persisted staff');$actual[]=(int)$id;}
  sort($actual,SORT_NUMERIC);if($actual!==$identity['staff_ids'])throw new \RuntimeException('Committed staff differs from fixture');
 }
}
