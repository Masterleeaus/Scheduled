<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** A slot preview cannot be mistaken for an irrevocable reservation. */
final class BookingSlotEvidenceGuard {
 public static function assertNotConfirmation(array $value):void {
  foreach(['confirmed','reserved','payment_captured'] as $key)if(($value[$key]??false)===true)throw new \DomainException('Uncommitted slot cannot claim confirmation');
 }
 public static function preview(array $data):array {
  self::assertNotConfirmation($data);
  return array_merge($data,['preview_only'=>true,'confirmed'=>false,'reserved'=>false,'payment_captured'=>false]);
 }
}
