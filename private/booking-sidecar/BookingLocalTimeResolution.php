<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Refuse nonexistent and ambiguous wall-clock starts: tenants must choose a unique instant. */
final class BookingLocalTimeResolution {
 public static function resolve(string $value,string $zone):\DateTimeImmutable {
  $dt=Interval::parse($value,$zone);
  $stamp=$dt->getTimestamp();$matches=[];
  // Verify all plausible timezone offsets in a 4-day window, including DST transitions.
  $tz=new \DateTimeZone($zone);
  $transitions=$tz->getTransitions($stamp-172800,$stamp+172800);
  $offsets=[$dt->getOffset()];
  if(is_array($transitions))foreach($transitions as $t)$offsets[]=(int)$t['offset'];
  $wall=\DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$value,new \DateTimeZone('UTC'));
  if(!$wall)throw new \InvalidArgumentException('Invalid wall clock');
  foreach(array_unique($offsets) as $offset){
   $instant=$wall->getTimestamp()-$offset;
   if((new \DateTimeImmutable('@'.$instant))->setTimezone($tz)->format('Y-m-d H:i:s')===$value)$matches[$instant]=true;
  }
  if(count($matches)!==1)throw new \DomainException('Nonexistent or ambiguous appointment start');
  return $dt;
 }
}
