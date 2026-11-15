<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Internal, read-only request. A public handler must still authorize tenant/staff ownership. */
final class AvailabilityPayload {
 public static function parse(array $p): array {
  $staff=filter_var($p['staff_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
  if ($staff===false) throw new InvalidArgumentException('Invalid staff id');
  $duration=filter_var($p['duration_minutes']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>15,'max_range'=>720]]);
  $step=filter_var($p['step_minutes']??15,FILTER_VALIDATE_INT,['options'=>['min_range'=>5,'max_range'=>120]]);
  if ($duration===false || $step===false) throw new InvalidArgumentException('Invalid duration or step');
  $zone=$p['timezone']??'UTC';
  if (!is_string($zone) || !is_string($p['date']??null) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D',$p['date'])) throw new InvalidArgumentException('Invalid date or zone');
  $midnight=Interval::parse($p['date'].' 00:00:00',$zone);
  if ($midnight->format('Y-m-d')!==$p['date']) throw new InvalidArgumentException('Invalid local day');
  return ['staff_id'=>(int)$staff,'duration_minutes'=>(int)$duration,'step_minutes'=>(int)$step,'date'=>$p['date'],'timezone'=>$zone];
 }
}
