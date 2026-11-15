<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Legacy staff ids are CSV; accept integers only and canonicalise for FIND_IN_SET. */
final class BookingStaff {
 public static function parse(mixed $value):array {
  if(!is_array($value)||count($value)<1||count($value)>12)throw new InvalidArgumentException('Staff required');
  $ids=[];foreach($value as $id){if(!is_int($id)||$id<1||$id>2147483647)throw new InvalidArgumentException('Invalid staff');$ids[]=$id;}
  $ids=array_values(array_unique($ids));sort($ids,SORT_NUMERIC);return $ids;
 }
 public static function toLegacyCsv(array $ids):string {return implode(',',self::parse($ids));}
}
