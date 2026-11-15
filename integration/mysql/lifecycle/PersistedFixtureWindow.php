<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedFixtureWindow {
 public static function from(array $fixture):array {
  $cmd=$fixture['create']??[];$zone=$cmd['timezone']??null;$minutes=$cmd['duration_minutes']??null;
  if(!is_string($zone)||!is_int($minutes))throw new \DomainException('Missing fixture timezone or duration');
  $identity=ConcurrencyFixtureIdentity::from($fixture);
  $range=PersistedInterval::fromLocal($identity['start_at'],$minutes,$zone);
  return ['interval'=>$range,'staff_ids'=>$identity['staff_ids'],'start_at'=>$identity['start_at'],'zone'=>$zone,'service_id'=>$identity['service_id']];
 }
}
