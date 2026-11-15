<?php
declare(strict_types=1);
spl_autoload_register(function($name){$prefix="App\\Booking\\Public\\Redemption\\";if(str_starts_with($name,$prefix)){require __DIR__."/../../core/app/Booking/Public/Redemption/".substr($name,strlen($prefix)).".php";}});
use App\Booking\Public\Redemption\{CommitBookingId,CommitConnection,CommitIntentScope,CommitEvidence,CommitEvidenceReader,CommitLedgerState,CommitReleaseGate,CommitResponse,CommitError,CommitReplayCheck,CommitCallback,CommitTransactionBoundary,CommitSameDatabase,CommitSelection,CommitCustomer,CommitOrchestrator};
try{CommitIntentScope::assert(['scope'=>'preview_only','v'=>1]);throw new Exception('accepted');}catch(RuntimeException $e){}
echo "PASS\n";
