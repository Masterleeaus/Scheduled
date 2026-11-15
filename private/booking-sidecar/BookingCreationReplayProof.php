<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Independently prove replay still refers to the exact active Cleanto appointment. */
final class BookingCreationReplayProof {
 public static function verifiedOrderId(\PDO $db,int $id,array $command):int {
  BookingAtomicPersistBoundary::assert($db);
  [$booking,$order]=BookingWriteProofReader::read($db,BookingPositiveId::parse($id));
  // A replay must match the persisted creation request. Never reprice an old booking
  // using rates which may have changed since the transaction committed.
  $rate=$booking['method_unit_qty_rate']??null;
  if(!is_string($rate)&&!is_numeric($rate))throw new \DomainException('Persisted replay rate missing');
  BookingWriteProofRows::check($booking,$order,$id,$command,(string)$rate);
  BookingAtomicPersistBoundary::assert($db);
  return BookingWriteProofId::check($booking['order_id']??null);
 }
}
