<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Internal-only integration. Never call with untrusted release evidence or public token. */
final class CommitCleantoNativeGateway {
 public static function execute(\PDO $db,int $owner,string $intentId,array $selection,array $customer,array $evidence,array $tenant,string $privateRoot,array $personalInfo=[]):array {
  CommitCleantoTenantPolicy::validate($owner,$tenant);
  // Sidecar location validation cannot alter authority from the trusted tenant configuration.
  CommitCleantoLoader::register($privateRoot);
  $personalInfo=\Titan\BookingSidecar\BookingPersonalInfo::parse($personalInfo);
  \Titan\BookingSidecar\BookingDriverGuard::assertMySql($db);
  \Titan\BookingSidecar\BookingTenantScopeGuard::assertConnection($db,$tenant);
  if($db->inTransaction())throw new \LogicException('Ambient transactions forbidden');
  $locks=CommitCleantoLocks::create($db);
  $writer=CommitCleantoWriteCallback::forTenant($tenant);
  $fresh=static function(array $selection,\PDO $db):void {
   if(!$db->inTransaction())throw new \LogicException('Expected transaction');
   // Authoritative price/slot checks are performed inside the native writer under locks.
  };
  return CommitLegacyGateway::execute($db,$owner,$intentId,$selection,$customer,$evidence,$writer,$fresh,$locks,$personalInfo);
 }
}
