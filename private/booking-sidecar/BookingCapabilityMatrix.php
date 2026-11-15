<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingCapabilityMatrix {
 public static function current():array {return [
  'service_catalogue'=>'read_only_adapter', 'busy_intervals'=>'read_only_adapter','staff_availability'=>'policy_tested_not_database_verified',
  'pricing'=>'exact_rule_static_audit_only','booking_creation'=>'disabled','cancellation'=>'disabled','rescheduling'=>'disabled',
  'recurrence'=>'planning_only','multi_tenant'=>'private_sidecar_contract_only','payments'=>'not_verified'
 ];}
}
