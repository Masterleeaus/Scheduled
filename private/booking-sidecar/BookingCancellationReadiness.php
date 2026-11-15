<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Explicitly classify live requirements without pretending unit tests are DB tests. */
final class BookingCancellationReadiness {
 public static function check(array $evidence):array {
  $requirements=['schema_verified','ownership_verified','legacy_status_verified','concurrency_verified','database_tested'];
  $missing=array_values(array_filter($requirements,fn($k)=>($evidence[$k]??false)!==true));
  return ['ready'=>$missing===[],'missing'=>$missing,'writes_enabled'=>false];
 }
}
