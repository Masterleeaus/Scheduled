<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewRequest {
 public static function parse(array $r): array {
  foreach(['date','timezone','service','staff_ids','duration_minutes'] as $k) if(!array_key_exists($k,$r)) throw new \InvalidArgumentException('Missing '.$k);
  $service=BookingPriceInput::parse($r['service']);
  $staff=BookingStaff::parse($r['staff_ids']);
  $slot=AvailabilityPayload::parse(['date'=>$r['date'],'timezone'=>$r['timezone'],'staff_id'=>$staff[0],'duration_minutes'=>$r['duration_minutes'],'step_minutes'=>$r['step_minutes']??15]);
  return ['service'=>$service,'staff_ids'=>$staff,'date'=>$slot['date'],'timezone'=>$slot['timezone'],'duration_minutes'=>$slot['duration_minutes'],'step_minutes'=>$slot['step_minutes']];
 }
}
