<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Strict legacy MySQL integer identifiers, protecting against silently cast garbage. */
final class BookingPositiveId {
 public static function parse(mixed $raw):int {
  if(is_int($raw))$value=$raw;
  elseif(is_string($raw)&&preg_match('/^[1-9][0-9]{0,9}$/D',$raw))$value=(int)$raw;
  else throw new \DomainException('Invalid persisted record identifier');
  if($value<1||$value>2147483647)throw new \DomainException('Persisted identifier out of range');
  return $value;
 }
}
