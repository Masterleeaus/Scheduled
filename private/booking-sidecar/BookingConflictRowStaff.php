<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Validate the original comma-separated Cleanto assignment column before trusting a SQL match. */
final class BookingConflictRowStaff {
 public static function members(mixed $csv):array {
  if(!is_string($csv)||$csv===''||strlen($csv)>2048)throw new \DomainException('Invalid persisted staff assignment');
  $ids=[];
  foreach(explode(',',$csv) as $part){$part=trim($part);if(!preg_match('/^[1-9][0-9]{0,9}$/D',$part))throw new \DomainException('Malformed staff assignment');$id=(int)$part;if(isset($ids[$id]))throw new \DomainException('Duplicate staff assignment');$ids[$id]=true;}
  return array_keys($ids);
 }
 public static function assertContains(mixed $csv,int $id):void {
  if($id<1||!in_array($id,self::members($csv),true))throw new \DomainException('Staff assignment mismatch');
 }
}
