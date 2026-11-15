<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Strict Cleanto signed INT identifier, accepting canonical integer strings as returned by PDO. */
final class LifecycleLegacyId {
 public static function parse(mixed $raw,string $field):int {
  if(is_int($raw))$v=$raw;
  elseif(is_string($raw)&&preg_match('/^[1-9][0-9]{0,9}$/D',$raw))$v=(int)$raw;
  else throw new \RuntimeException('Invalid legacy identifier: '.$field);
  if($v<1||$v>2147483647)throw new \RuntimeException('Legacy identifier out of range: '.$field);
  return $v;
 }
}
