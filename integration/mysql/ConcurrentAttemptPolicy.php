<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrentAttemptPolicy {
 public static function evaluate(array $outcomes):array {
  if(count($outcomes)!==2)throw new \InvalidArgumentException('Two simultaneous attempts required');
  $success=0;$failure=0;
  foreach($outcomes as $outcome){if($outcome==='confirmed')$success++;elseif(in_array($outcome,['conflict','lock_unavailable'],true))$failure++;else throw new \DomainException('Unknown concurrent result');}
  return ['pass'=>$success===1&&$failure===1,'confirmed'=>$success,'rejected'=>$failure];
 }
}
