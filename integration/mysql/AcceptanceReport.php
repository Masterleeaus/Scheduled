<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class AcceptanceReport {
 public static function build(array $missing,array $badEngines,bool $rollback,bool|array $locks):array {
  $lockResult=is_array($locks) ? LockProbeResult::verified($locks) : $locks;
  $ok=(!$missing&&!$badEngines&&$rollback&&$lockResult);
  return ['accepted'=>$ok,'scope'=>'database_infrastructure_only','schema_missing'=>$missing,'nontransactional_tables'=>$badEngines,'rollback_ok'=>$rollback,'advisory_lock_ok'=>$lockResult,'booking_creation_verified'=>false,'concurrency_verified'=>false];
 }
}
