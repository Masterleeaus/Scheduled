<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyFixtureIdentity {
 public static function from(array $fixture):array {
  $cmd=$fixture['create']??null;if(!is_array($cmd))throw new \DomainException('Missing fixture create command');
  $service=$cmd['service']??null;$ids=$cmd['staff_ids']??null;$start=$cmd['start_at']??null;
  if(!is_array($service)||!filter_var($service['service_id']??null,FILTER_VALIDATE_INT)||!is_array($ids)||!$ids||!is_string($start)||!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D',$start))throw new \DomainException('Invalid readback identity');
  LifecyclePersistedTimestamp::parse($start);
  $staff=array_map(static function($id):int {return LifecycleLegacyId::parse($id,'staff');},$ids);sort($staff,SORT_NUMERIC);
  if(count(array_unique($staff))!==count($staff))throw new \DomainException('Duplicate staff');
  return ['service_id'=>(int)$service['service_id'],'start_at'=>$start,'staff_ids'=>$staff];
 }
}
