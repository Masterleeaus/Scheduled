<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewIntersection {
 public static function all(array $staffSlots):array {
  if(!$staffSlots)return [];
  $counts=[];
  foreach($staffSlots as $slots){
   $unique=[];
   foreach($slots as $s)$unique[BookingPreviewSlot::timestamp($s)]=true;
   foreach(array_keys($unique) as $t)$counts[$t]=($counts[$t]??0)+1;
  }
  $result=[];
  foreach($counts as $t=>$count)if($count===count($staffSlots))$result[]=(int)$t;
  sort($result,SORT_NUMERIC);return $result;
 }
}
