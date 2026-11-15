<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedOverlapCounter {
 public static function count(array $rows,array $window):array {
  PersistedScanPolicy::assertBounded($rows);$matches=[];$seen=[];
  foreach($rows as $row){$r=PersistedRowPolicy::normalize($row,$window['zone']);if(isset($seen[$r['id']]))throw new \RuntimeException('Duplicate booking rows from join');$seen[$r['id']]=true;
   if($r['occupied'] && PersistedStaffSet::intersects($r['staff_ids'],$window['staff_ids']) && PersistedInterval::overlap($r['interval'],$window['interval']))$matches[]=$r['id'];
  }
  sort($matches,SORT_NUMERIC);return $matches;
 }
}
