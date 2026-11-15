<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewStaffPolicy {
 public static function assertCover(array $asked,array $results):void {
  $ids=BookingStaff::parse($asked);
  $keys=array_keys($results);$keys=array_map('intval',$keys);sort($keys,SORT_NUMERIC);
  if($keys!==$ids)throw new \DomainException('Missing staff availability response');
 }
}
