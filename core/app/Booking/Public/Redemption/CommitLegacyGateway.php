<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** INTERNAL ONLY. Accepts trusted server-side configuration, not public request fields. */
final class CommitLegacyGateway {
 public static function execute(\PDO $db,int $owner,string $intentId,array $selection,array $customer,array $releaseEvidence,callable $persist,callable $freshCheck,CommitLegacyLockBoundary $locks,array $personalInfo=[]):array {
  CommitReleaseEvidencePolicy::validate($releaseEvidence);
  $s=CommitSelection::parse($selection);$c=CommitLegacyCustomer::map($customer);
  $key=CommitLegacyClaimKey::derive($intentId);
  // Validate location first and bind it to idempotency so the same intent
  // cannot be replayed with a different service address or customer notes.
  $personalInfo=\Titan\BookingSidecar\BookingPersonalInfo::parse($personalInfo);
  $fingerprint=CommitLegacyFingerprint::make($owner,$s,$c,$personalInfo);
  $writer=CommitLegacyAdapter::writer($persist,$key,$personalInfo);
  return CommitOrchestrator::execute($db,new RedeemLedger($db),$key,$owner,$fingerprint,$s,$c,$writer,$freshCheck,
   fn($db,$s)=>$locks->before($db,$s),fn($db)=>$locks->after($db));
 }
}
