<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** A completed request must not be reissued; mismatched fingerprints always fail. */
final class BookingLedgerConflictPolicy {
 public static function classify(?array $record,string $fingerprint):string {
  if(!preg_match('/^[a-f0-9]{64}$/D',$fingerprint))throw new \InvalidArgumentException('Invalid fingerprint');
  if($record===null)return 'new';
  if(!isset($record['fingerprint'])||!hash_equals($record['fingerprint'],$fingerprint))throw new \DomainException('Request key reused');
  return match($record['state']??''){'completed'=>'replay','pending'=>'pending','failed'=>'failed',default=>throw new \DomainException('Unknown ledger state')};
 }
}
