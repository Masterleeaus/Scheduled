<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Creates a trusted writer and lock lease for CommitLegacyGateway without duplicate SQL transactions. */
final class BookingAtomicBridge {
 public static function writer(\PDO $db,array $verifiedPricing):callable {
  return static function(\PDO $connection,array $cmd)use($db,$verifiedPricing):int {
   if($connection!==$db)throw new \RuntimeException('Claim and booking must share PDO');
   return (new BookingAtomicPersist($db,$verifiedPricing))->insert($cmd);
  };
 }
 public static function lockCallbacks(\PDO $db):array {
  $lease=new BookingAtomicLockLease($db);
  return [static function(\PDO $connection,array $selection)use($db,$lease):void {
   if($connection!==$db)throw new \RuntimeException('Lock connection differs');
   $lease->acquire([$selection['staff_id']]);
  },static function(\PDO $connection)use($db,$lease):void {
   if($connection!==$db)throw new \RuntimeException('Lock connection differs');
   $lease->release();
  }];
 }
}
