<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Verify assignments fit legacy varchar(160) and contain no duplicate IDs. */
final class BookingLiveWriteStaff {
 public static function assert(array $staff):void {
  if(!$staff)throw new \InvalidArgumentException('Missing staff assignment');
  $seen=[];foreach($staff as $id){if(!is_int($id))throw new \InvalidArgumentException('Invalid staff ID');BookingLiveWriteIds::foreignId($id);if(isset($seen[$id]))throw new \InvalidArgumentException('Duplicate staff');$seen[$id]=true;}
  $csv=implode(',',array_keys($seen));if(strlen($csv)>160)throw new \InvalidArgumentException('Legacy staff column overflow');
 }
}
