<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Only persisted, occupying Cleanto statuses participate in conflict decisions. */
final class BookingConflictRowStatus {
 public static function active(mixed $status):bool {
  if(!is_string($status)||!in_array($status,['A','C','RS'],true))throw new \DomainException('Unexpected persisted busy-row status');
  return true;
 }
}
