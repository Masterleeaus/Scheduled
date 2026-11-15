<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingQuantityPolicy {
 public static function check(mixed $qty,mixed $min,mixed $max):int {
  foreach([$qty,$min,$max] as $v)if(!is_int($v)&&!(is_string($v)&&ctype_digit($v)))throw new \InvalidArgumentException('Quantity not integer');
  $q=(int)$qty;$lo=(int)$min;$hi=(int)$max;
  if($lo<1||$hi<$lo||$hi>10000||$q<$lo||$q>$hi)throw new \DomainException('Quantity outside configured bounds');return $q;
 }
}
