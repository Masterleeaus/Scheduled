<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Refuse reuse of the same key for a different payload or an incomplete write. */
final class BookingMutationReplay {
 public static function evaluate(?array $row,string $fingerprint):?int {
  if($row===null)return null;
  if(!hash_equals((string)($row['fingerprint']??''),$fingerprint))throw new \DomainException('Idempotency key reused for different mutation');
  if(BookingLedgerState::read($row)!=='completed')throw new \DomainException('Mutation still pending');
  $id=BookingPositiveId::parse($row['booking_id']??null);
  return $id;
 }
}
