<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingServicePriceSnapshot {
 public static function fromRow(array $r):array {
  foreach(['id','services_id','methods_id','minlimit','maxlimit','base_price'] as $k)if(!array_key_exists($k,$r))throw new \DomainException('Incomplete Cleanto unit');
  if(($r['status']??'')!=='E')throw new \DomainException('Unit disabled');
  $id=(int)$r['id'];$s=(int)$r['services_id'];$m=(int)$r['methods_id'];if(min($id,$s,$m)<1)throw new \DomainException('Invalid service ids');
  return ['id'=>$id,'service_id'=>$s,'method_id'=>$m,'min'=>(int)$r['minlimit'],'max'=>(int)$r['maxlimit'],'base_cents'=>BookingPriceDecimal::cents((string)$r['base_price'])];
 }
}
