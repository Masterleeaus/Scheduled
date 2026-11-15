<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitCustomerIdentityPolicy {
 /** Identity claims from public forms are data only; they never establish tenant authority. */
 public static function requireSafe(array $customer):array {
  $result=CommitLegacyCustomer::map($customer);
  if(preg_match('/[\r\n\x00-\x1f\x7f]/',$result['name'].$result['email'].$result['phone']))throw new \InvalidArgumentException('Control characters in customer data');
  return $result;
 }
}
