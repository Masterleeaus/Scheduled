<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Canonical schema for durable booking requests; guards against conflicting earlier SQL. */
final class BookingLedgerSchemaContract {
 public const TABLE='titan_booking_requests';
 public static function required():array {return ['request_key','fingerprint','state','booking_id','created_at','updated_at'];}
 public static function diff(array $columns):array {return array_values(array_diff(self::required(),$columns));}
 public static function assertCompatible(array $columns):void { $missing=self::diff($columns);if($missing)throw new \DomainException('Booking ledger schema mismatch: '.implode(',',$missing)); }
}
