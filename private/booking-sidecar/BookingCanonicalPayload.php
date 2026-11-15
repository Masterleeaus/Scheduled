<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Stable associative-key canonicalization without changing the order of JSON lists. */
final class BookingCanonicalPayload {
 public static function normalize(array $payload,int $depth=0):array {
  if($depth>32)throw new \InvalidArgumentException('Command nesting too deep');
  if(array_is_list($payload))return array_map(static fn($value)=>is_array($value)?self::normalize($value,$depth+1):self::scalar($value),$payload);
  ksort($payload,SORT_STRING);
  foreach($payload as &$value){$value=is_array($value)?self::normalize($value,$depth+1):self::scalar($value);}unset($value);
  return $payload;
 }
 private static function scalar(mixed $value):mixed {
  if(is_object($value)||is_resource($value)||is_float($value)&&!is_finite($value))throw new \InvalidArgumentException('Unsupported booking command value');
  return $value;
 }
}
