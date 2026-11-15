<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO; use DomainException;
/** Every request-ledger operation fails closed under PDO silent error mode. */
final class BookingRequestLedger {
 public function __construct(private PDO $db){}
 private function requireTransaction():void {if(!$this->db->inTransaction())throw new \LogicException('Request ledger requires transaction');}
 public function getForUpdate(string $key):?array {
  $this->requireTransaction();$key=BookingKey::normalize($key);
  $s=$this->db->prepare('SELECT fingerprint, state, booking_id FROM titan_booking_requests WHERE request_key=? FOR UPDATE');
  if($s===false||$s->execute([$key])!==true)throw new \RuntimeException('Request ledger lookup failed');
  $row=$s->fetch(PDO::FETCH_ASSOC);if($row===false)return null;
  if(!is_array($row)||$s->fetch(PDO::FETCH_ASSOC)!==false)throw new DomainException('Ambiguous request ledger row');
  BookingLedgerState::read($row);return $row;
 }
 public function claim(string $key,string $fingerprint):void {
  $this->requireTransaction();$key=BookingKey::normalize($key);
  if(!preg_match('/^[a-f0-9]{64}$/D',$fingerprint))throw new \InvalidArgumentException('Invalid fingerprint');
  $s=$this->db->prepare("INSERT INTO titan_booking_requests (request_key, fingerprint, state) VALUES (?, ?, 'pending')");
  if($s===false||$s->execute([$key,$fingerprint])!==true)throw new \RuntimeException('Request ledger claim failed');
  BookingAffectedRows::inserted($s,'Request ledger claim');
 }
 public function complete(string $key,int $bookingId):void {
  $this->requireTransaction();$key=BookingKey::normalize($key);BookingPositiveId::parse($bookingId);
  $s=$this->db->prepare("UPDATE titan_booking_requests SET state='completed',booking_id=? WHERE request_key=? AND state='pending'");
  if($s===false||$s->execute([$bookingId,$key])!==true)throw new \RuntimeException('Request ledger completion failed');
  if($s->rowCount()!==1)throw new DomainException('Idempotency completion not recorded');
 }
}
