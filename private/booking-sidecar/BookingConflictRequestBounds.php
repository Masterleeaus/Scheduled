<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** An availability scan is bounded so a malformed request cannot trigger an unbounded query. */
final class BookingConflictRequestBounds {
 public static function validate(int $staff,string $from,string $until,string $zone):array {
  if($staff<1)throw new \InvalidArgumentException('Invalid staff');
  [$a,$b]=BookingConflictQueryWindow::validate($from,$until,$zone);
  return [$a,$b];
 }
}
