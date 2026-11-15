<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitReleaseEvidencePolicy {
 /** Readiness flags cannot be supplied by an anonymous customer endpoint. */
 public static function validate(array $evidence):void {
  CommitLegacyRelease::require($evidence);
  foreach(['same_database_transaction','independent_persisted_readback','staff_locks_held_until_commit'] as $key){
   if(($evidence[$key]??null)!==true)throw new \RuntimeException('Missing independent release evidence: '.$key);
  }
 }
}
