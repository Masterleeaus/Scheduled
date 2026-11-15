<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Caller idempotency key is intentionally independent of order numbers. */
final class BookingKey {
 public static function normalize(mixed $value):string {
  if(!is_string($value)||!preg_match('/^[a-zA-Z0-9_-]{20,96}$/D',$value))throw new InvalidArgumentException('Invalid idempotency key');
  return $value;
 }
 public static function fingerprint(array $data):string {return hash('sha256',json_encode(BookingCanonicalPayload::normalize($data),JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES));}
}
