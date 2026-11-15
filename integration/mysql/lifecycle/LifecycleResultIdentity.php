<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Keep mutation responses constrained to actual persisted legacy IDs. */
final class LifecycleResultIdentity {
 public static function assert(array $response,int $expected,string $operation):void {
  if($expected<1||$expected>2147483647||!is_int($response['booking_id']??null)||$response['booking_id']!==$expected)throw new \RuntimeException($operation.' reported inconsistent booking ID');
 }
}
