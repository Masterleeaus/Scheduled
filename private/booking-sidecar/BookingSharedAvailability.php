<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Do not trust an availability preview as an actual reservation. */
final class BookingSharedAvailability {
 public static function assert(\PDO $db,array $command):void {
  (new BookingFinalAvailability($db))->assertAvailable($command);
  (new BookingConflictGuard($db))->assertFree($command);
 }
}
