<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Unified booking input: never trust caller-provided payment totals or tenant identifiers. */
final class BookingCommand {
 public static function create(array $r):array {
  $key=BookingKey::normalize($r['idempotency_key']??null);$staff=BookingStaff::parse($r['staff_ids']??null);
  $service=BookingService::parse($r['service']??[]);$customer=BookingCustomer::parse($r['customer']??[]);
  if(!is_string($r['start_at']??null)||!is_string($r['timezone']??null))throw new InvalidArgumentException('Invalid start');
  $duration=$r['duration_minutes']??null;if(!is_int($duration))throw new InvalidArgumentException('Invalid duration');
  $window=BookingTimeWindow::parse($r['start_at'],$duration,$r['timezone']);
  // Keep optional service-location data in the transaction command.
  $personalInfo=BookingPersonalInfo::parse($r['personal_info']??[]);
  return ['idempotency_key'=>$key,'staff_ids'=>$staff,'service'=>$service,'customer'=>$customer,'personal_info'=>$personalInfo,'duration_minutes'=>$duration,'timezone'=>$r['timezone'],'window'=>$window];
 }
}
