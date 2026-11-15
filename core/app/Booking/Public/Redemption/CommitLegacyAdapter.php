<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Callback adapter accepts the existing Cleanto writer on the SAME PDO transaction. */
final class CommitLegacyAdapter {
 public static function writer(callable $persist,string $key,array $personalInfo=[]):callable {
  return static function(array $selection,array $customer,\PDO $db)use($persist,$key,$personalInfo):int {
   if(!$db->inTransaction())throw new \RuntimeException('No open claim transaction');
   $cmd=CommitLegacySelection::map($selection,CommitCustomerIdentityPolicy::requireSafe($customer),$key);
   // Personal location data never supplies pricing, tenant, service, or staff authority.
   $cmd['personal_info']=\Titan\BookingSidecar\BookingPersonalInfo::parse($personalInfo);
   $id=CommitBookingId::parse($persist($db,$cmd));
   CommitTransactionBoundary::assert($db);
   CommitPersistenceProof::assert($db,$id,$selection);
   return $id;
  };
 }
}
