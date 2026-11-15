<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Only original Cleanto statuses which consume staff availability count as occupied. */
final class PersistedStatusPolicy {
 public const OCCUPIED=['A','C','RS'];
 public static function occupies(string $status):bool {
  if($status==='' || strlen($status)>16)throw new \DomainException('Invalid booking status');
  return in_array($status,self::OCCUPIED,true);
 }
}
