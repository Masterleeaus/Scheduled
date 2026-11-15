<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Strict staff ID normalization for Cleanto's comma-delimited staff_ids column. */
final class PersistedStaffSet {
 public static function parse(string $raw):array {
  if($raw==='' || strlen($raw)>4096)throw new \DomainException('Invalid stored staff list');
  $out=[];foreach(explode(',',$raw) as $part){$part=trim($part);if(!ctype_digit($part)||(int)$part<1 || (string)(int)$part!==$part)throw new \DomainException('Invalid stored staff id');$out[]=(int)$part;}
  if(count($out)!==count(array_unique($out)))throw new \DomainException('Duplicate persisted staff id');sort($out,SORT_NUMERIC);return $out;
 }
 public static function intersects(array $a,array $b):bool{return (bool)array_intersect($a,$b);}
}
