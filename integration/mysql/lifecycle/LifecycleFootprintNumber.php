<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Validate nonnegative exact SQL aggregate counts without float conversion. */
final class LifecycleFootprintNumber {
 public static function parse(mixed $value):int {
  if(!is_int($value)&&!is_string($value))throw new \RuntimeException('Invalid SQL count type');
  $raw=(string)$value;
  if(!preg_match('/^(0|[1-9][0-9]*)$/D',$raw))throw new \RuntimeException('Invalid SQL count value');
  if(strlen($raw)>strlen((string)PHP_INT_MAX)||(strlen($raw)===strlen((string)PHP_INT_MAX)&&strcmp($raw,(string)PHP_INT_MAX)>0))throw new \RuntimeException('SQL count overflow');
  return (int)$raw;
 }
}
