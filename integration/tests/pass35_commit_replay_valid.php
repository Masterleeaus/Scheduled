<?php
declare(strict_types=1);
spl_autoload_register(function($name){$prefix="App\\Booking\\Public\\Redemption\\";if(str_starts_with($name,$prefix)){require __DIR__."/../../core/app/Booking/Public/Redemption/".substr($name,strlen($prefix)).".php";}});
use App\Booking\Public\Redemption\{CommitBookingId,CommitConnection,CommitIntentScope,CommitEvidence,CommitEvidenceReader,CommitLedgerState,CommitReleaseGate,CommitResponse,CommitError,CommitReplayCheck,CommitCallback,CommitTransactionBoundary,CommitSameDatabase,CommitSelection,CommitCustomer,CommitOrchestrator};
$r=CommitReplayCheck::resolve(['owner_id'=>2,'fingerprint'=>str_repeat('a',64),'state'=>'committed','booking_id'=>10],2,str_repeat('a',64),fn($id)=>['id'=>$id,'booking_status'=>'A','client_id'=>2,'booking_date_time'=>'2026-11-12 09:00:00']);if(!$r['replay'])throw new Exception('replay');
echo "PASS\n";
