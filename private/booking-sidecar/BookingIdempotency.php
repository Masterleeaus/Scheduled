<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use DomainException;
/** Pure decision layer for eventual durable unique-key persistence. */
final class BookingIdempotency {
 public static function decide(?array $stored,string $fingerprint):string {
  if($stored===null)return 'new';
  if(!preg_match('/^[a-f0-9]{64}$/D',$fingerprint))throw new DomainException('Invalid fingerprint');
  if(!hash_equals((string)($stored['fingerprint']??''),$fingerprint))throw new DomainException('Idempotency key reused for different command');
  $state=BookingLedgerState::read($stored);return $state==='completed'?'replay':'pending';
 }
}
