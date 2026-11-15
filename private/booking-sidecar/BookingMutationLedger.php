<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Separate namespace for cancellation keys prevents collision with booking creation keys. */
final class BookingMutationLedger {
 public function __construct(private \PDO $db){}
 public function forUpdate(string $key):?array {return (new BookingRequestLedger($this->db))->getForUpdate('m_'.hash('sha256',BookingKey::normalize($key)));}
 public function claim(string $key,string $fingerprint):void {(new BookingRequestLedger($this->db))->claim('m_'.hash('sha256',BookingKey::normalize($key)),$fingerprint);}
 public function complete(string $key,int $id):void {(new BookingRequestLedger($this->db))->complete('m_'.hash('sha256',BookingKey::normalize($key)),$id);}
}
