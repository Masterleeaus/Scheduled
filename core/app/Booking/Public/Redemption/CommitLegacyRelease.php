<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Configuration is server-owned; tests must never self-certify release evidence. */
final class CommitLegacyRelease {
 public static function require(array $config):void {
  if(($config['public_writes_enabled']??null)!==true)throw new \RuntimeException('Public bookings disabled');
  $fields=['mysql_lifecycle_passed','concurrency_passed','pricing_parity_passed','tenant_isolation_passed','schema_passed'];
  foreach($fields as $key)if(($config[$key]??null)!==true)throw new \RuntimeException('Unverified booking release: '.$key);
 }
}
