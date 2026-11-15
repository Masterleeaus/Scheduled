<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Acceptance infrastructure alone cannot release booking writes. */
final class BookingWriteReleaseBlocker {
 public static function requireLifecycleEvidence(array $evidence):void {
  foreach(['real_insert','concurrent_overlap','idempotency','cancel','reschedule','pricing_parity','tenant_isolation'] as $key){
   if(($evidence[$key]??false)!==true)throw new \DomainException('Missing lifecycle evidence: '.$key);
  }
 }
}
