<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitLegacyClaimKey {
 public static function derive(string $intentId):string {
  if(!preg_match('/^[a-zA-Z0-9_-]{20,160}$/D',$intentId))throw new \InvalidArgumentException('Invalid intent identifier');
  return hash('sha256','titan-booking-claim-v1:'.$intentId);
 }
}
