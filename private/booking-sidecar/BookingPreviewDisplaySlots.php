<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewDisplaySlots {
 public static function local(array $times,string $zone,int $limit=200):array {
  if($limit<1||$limit>500)throw new \InvalidArgumentException('Invalid slot cap');
  if(count($times)>$limit)throw new \DomainException('Too many available slots');
  $tz=new \DateTimeZone($zone);$out=[];
  foreach($times as $t){
   if(!is_int($t))throw new \DomainException('Bad timestamp');
   $out[]=(new \DateTimeImmutable('@'.$t))->setTimezone($tz)->format('Y-m-d H:i:sP');
  }
  return $out;
 }
}
