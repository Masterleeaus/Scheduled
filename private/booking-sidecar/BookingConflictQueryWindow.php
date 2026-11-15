<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Query range must encompass the complete occupied window, with no negative ranges. */
final class BookingConflictQueryWindow {
 public static function validate(string $from,string $until,string $zone):array {
  $a=BookingLocalTimeResolution::resolve($from,$zone);
  $b=BookingLocalTimeResolution::resolve($until,$zone);
  if($b->getTimestamp()<=$a->getTimestamp()||$b->getTimestamp()-$a->getTimestamp()>8*86400)throw new \InvalidArgumentException('Conflict range invalid');
  return [$a,$b];
 }
}
