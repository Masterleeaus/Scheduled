<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewLimit {
 public static function enforce(array $request):void {
  if(count($request['staff_ids'])>12)throw new \DomainException('Too many staff');
  if($request['step_minutes']<5||$request['step_minutes']>120)throw new \DomainException('Unsupported step');
  $date=Interval::parse($request['date'].' 00:00:00',$request['timezone']);
  if($date->getTimestamp()<strtotime('2020-01-01 UTC'))throw new \DomainException('Historical date unsupported');
 }
}
