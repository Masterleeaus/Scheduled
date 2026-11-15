<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/**
 * Default-off admission for the contract operations booking_cancel and booking_reschedule.
 * Uses the same write flags as booking_create (BookingWriteFeatureGate) plus the tenant's
 * owner-operations flag. Any missing flag means the operation is treated as unsupported.
 */
final class BookingLifecycleGate {
 public const OPERATIONS=['booking_cancel','booking_reschedule'];
 public static function enabled(array $config):bool {
  return BookingWriteFeatureGate::enabled($config)&&(($config['owner_operations_enabled']??false)===true);
 }
 public static function handles(string $operation):bool {return in_array($operation,self::OPERATIONS,true);}
 public static function ownerOperation(string $operation):string {
  return match($operation){
   'booking_cancel'=>'owner_booking_cancel',
   'booking_reschedule'=>'owner_booking_reschedule',
   default=>throw new \InvalidArgumentException('Unsupported booking operation'),
  };
 }
}
