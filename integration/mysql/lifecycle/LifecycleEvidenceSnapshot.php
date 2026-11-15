<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Evidence contains authoritative server-side IDs only; no customer PII. */
final class LifecycleEvidenceSnapshot {
 public static function from(array $row):array {
  return ['booking_id'=>$row['id'],'order_id'=>$row['order_id'],'service_id'=>$row['service_id'],'staff_count'=>count($row['staff_ids']),'status'=>$row['booking_status']];
 }
}
