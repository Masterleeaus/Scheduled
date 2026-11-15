<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Provides the native writer to the existing redemption gateway. */
final class CommitCleantoWriteCallback {
 public static function forTenant(array $tenant):callable {
  $writer=new \Titan\BookingSidecar\BookingSharedTransactionWriter($tenant);
  return static function(\PDO $db,array $command)use($writer):int {return $writer->write($db,$command);};
 }
}
