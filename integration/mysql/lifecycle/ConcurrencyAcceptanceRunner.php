<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyAcceptanceRunner {
 public static function run(array $env,array $cfg,array $fixture,string $fixturePath):array {
  LifecycleConsent::assert($env);LifecycleTestScope::assert($cfg,$fixture);ConcurrencyFixturePolicy::assert($fixture);
  if(($env['BOOKING_ACCEPT_TEST_ONLY']??null)!=='YES_DISPOSABLE_ONLY')throw new \DomainException('Test-only flag required');
  $id=ConcurrencyRunId::make();$dir=ConcurrencyDirectory::create($id);$children=[];
  try {foreach(['a','b'] as $w)$children[]=ConcurrencyProcess::launch($w,$dir,$fixturePath,$env);
   ConcurrencySupervisor::waitReady($dir,$children);ConcurrencySupervisor::release($dir,$children);
   ConcurrencyProcessGroup::finishAll($children);
   $children=[];$a=ConcurrencyResultStore::read($dir,'a');$b=ConcurrencyResultStore::read($dir,'b');
   $outcome=ConcurrencyOutcome::assertSingleWinner($a,$b);
   $db=MysqlTestConnection::open($cfg);ConcurrencyEvidencePreflight::assert($db,$cfg['database']);
   $databaseEvidence=PersistedConcurrencyEvidence::verify($db,$outcome['accepted_booking_id'],$fixture);
   PersistedEvidenceGate::assert($databaseEvidence,$outcome['accepted_booking_id']);
   $evidence=ConcurrencyOutcomeEvidence::merge($outcome,$databaseEvidence);
   return ConcurrencyEvidenceReport::attach(ConcurrencyAcceptanceReport::make($evidence,$cfg['database']),$evidence);
  }finally {foreach($children as $c)ConcurrencyProcess::terminate($c);ConcurrencyDirectory::clear($dir);}
 }
}
