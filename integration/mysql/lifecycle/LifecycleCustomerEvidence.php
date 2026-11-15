<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Only compare non-sensitive fingerprints from customer fixtures and stored order. */
final class LifecycleCustomerEvidence {
 public static function verify(array $order,array $create):void {
  $email=$create['customer']['email']??null;
  if(!is_string($email)||!filter_var($email,FILTER_VALIDATE_EMAIL))throw new \RuntimeException('Invalid customer fixture email');
  if(!hash_equals($order['email_fingerprint'],hash('sha256',strtolower(trim($email)))))throw new \RuntimeException('Stored customer identity mismatch');
 }
}
