<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Parse stored unit rates as exact decimal cents, without floats. */
final class LifecyclePersistedRate {
 public static function parse(mixed $raw):int {
  if(!is_string($raw)&&!is_int($raw))throw new \RuntimeException('Invalid stored rate type');
  $v=(string)$raw;
  if(!preg_match('/^(0|[1-9]\d{0,8})(?:\.(\d{1,2}))?$/D',$v))throw new \RuntimeException('Malformed stored unit rate');
  [$whole,$fraction]=array_pad(explode('.',$v,2),2,'');
  return (int)$whole*100+(int)str_pad($fraction,2,'0');
 }
}
