<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Combines declared validation evidence into a fail-closed write release decision. */
final class BookingWriteSafetyAudit {
 public static function evaluate(array $checks):array {
  $required=['tenant_scope','ownership_authorization','schema_verified','pricing_parity',
   'staff_availability','overlap_recheck','parallel_reservation_test','rollback_test',
   'payment_reconciliation','idempotency_replay'];
  $missing=[];foreach($required as $name)if(($checks[$name]??false)!==true)$missing[]=$name;
  return ['allow_write'=>false,'evidence_complete'=>!$missing,'missing'=>$missing];
 }
}
