<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingRateTable {
 public static function normalize(array $rows):array {
  if(!$rows||count($rows)>500)throw new \DomainException('Invalid rate table');$out=[];
  foreach($rows as $r){$v=BookingRateRule::parse($r);$k=$v['rule'].':'.$v['units'];if(isset($out[$k]))throw new \DomainException('Duplicate rate rule');$out[$k]=$v;}
  return array_values($out);
 }
 public static function matchExact(array $rules,int $quantity):int {
  $matches=array_values(array_filter($rules,fn($r)=>$r['rule']==='E'&&$r['units']===$quantity));
  if(count($matches)!==1)throw new \DomainException('Exact rule cannot be verified');return $matches[0]['rate_cents'];
 }
}
