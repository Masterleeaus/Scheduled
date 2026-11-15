<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Reject joins producing corrupted or duplicate identity; never silently discard bookings. */
final class BookingConflictRowIdentity {
 public static function parse(array $row):int {
  $raw=$row['id']??null;
  if(is_int($raw)&&$raw>0)return $raw;
  if(is_string($raw)&&preg_match('/^[1-9][0-9]*$/D',$raw)&&strlen($raw)<=19)return (int)$raw;
  throw new \DomainException('Invalid occupied booking ID');
 }
 public static function unique(array $rows):void {
  $seen=[];
  foreach($rows as $row){$id=self::parse($row);if(isset($seen[$id]))throw new \DomainException('Duplicate booking join row');$seen[$id]=true;}
 }
}
