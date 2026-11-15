<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Fail closed before the external redemption transaction marks a booking complete. */
final class BookingAtomicPersistProof {
 public static function verify(\PDO $db,int $id,array $command,string $rate):void {
  BookingAtomicPersistBoundary::assert($db);
  BookingWritePostCommitGate::verify($db,$id,$command,$rate);
  BookingAtomicPersistBoundary::assert($db);
 }
}
