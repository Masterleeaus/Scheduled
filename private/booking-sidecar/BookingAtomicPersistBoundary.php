<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Makes accidental implicit commit by any legacy writer a hard failure. */
final class BookingAtomicPersistBoundary {
 public static function assert(\PDO $db):void {
  if(!$db->inTransaction())throw new \LogicException('Legacy writer closed caller-owned booking transaction');
 }
}
