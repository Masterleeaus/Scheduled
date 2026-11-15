<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewSlot {
 public static function timestamp(mixed $v):int {
  if(is_int($v)) return $v;
  if(is_string($v) && preg_match('/^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}[+-]\\d{2}:\\d{2}$/D',$v)) {
   $dt=\DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i:sP',$v);
   if(!$dt)throw new \DomainException('Malformed ISO slot');
   return $dt->getTimestamp();
  }
  if(is_array($v)) {foreach(['start','start_timestamp','timestamp'] as $k)if(isset($v[$k])&&is_int($v[$k]))return $v[$k];}
  throw new \DomainException('Unsupported slot representation');
 }
}
