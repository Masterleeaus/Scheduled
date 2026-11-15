<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Advisory locks are acquired BEFORE BEGIN and held until AFTER commit/rollback. */
final class CommitOrchestrator {
 public static function execute(\PDO $db,RedeemLedger $ledger,string $key,int $owner,string $fingerprint,array $selection,array $customer,callable $writer,callable $freshCheck,?callable $before=null,?callable $after=null):array {
  if($db->inTransaction())throw new \RuntimeException('Outer transaction unsupported');
  $acquired=false;
  try {
   if($before!==null){ $before($db,$selection); $acquired=true; }
   return RedeemTransaction::run($db,static function()use($db,$ledger,$key,$owner,$fingerprint,$selection,$customer,$writer,$freshCheck){
    CommitTransactionBoundary::assert($db);
    $claimed=$ledger->claim($key,$owner,$fingerprint);$row=$ledger->read($key);
    if(!$row)throw new \RuntimeException('Claim row not found');
    $state=CommitLedgerState::validate($row,$owner,$fingerprint);
    if(!$claimed){if($state==='committed')return CommitReplayCheck::resolve($row,$owner,$fingerprint,static function($id)use($db,$selection){$persisted=CommitReplayPersistedReader::read($db,$id);CommitReplaySelectionEvidence::check($persisted,$selection,$id);CommitReplayOrderProof::assert($db,$id,$selection);return $persisted;});throw new \RuntimeException('Claim already in progress');}
    if($state!=='claimed')throw new \RuntimeException('Unexpected claim state');
    $freshCheck($selection,$db);
    $id=CommitCallback::call($writer,$selection,$customer,$db);
    CommitPersistenceProof::assert($db,$id,$selection);
    if(!$ledger->commit($key,$owner,$fingerprint,$id))throw new \RuntimeException('Failed to commit claim');
    return CommitResponse::confirmed($id);
   });
  } finally {if($acquired&&$after!==null)$after($db);}
 }
}
