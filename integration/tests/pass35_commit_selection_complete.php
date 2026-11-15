<?php
declare(strict_types=1);
spl_autoload_register(function($name){$prefix="App\\Booking\\Public\\Redemption\\";if(str_starts_with($name,$prefix)){require __DIR__."/../../core/app/Booking/Public/Redemption/".substr($name,strlen($prefix)).".php";}});
use App\Booking\Public\Redemption\{CommitBookingId,CommitConnection,CommitIntentScope,CommitEvidence,CommitEvidenceReader,CommitLedgerState,CommitReleaseGate,CommitResponse,CommitError,CommitReplayCheck,CommitCallback,CommitTransactionBoundary,CommitSameDatabase,CommitSelection,CommitCustomer,CommitOrchestrator};
$s=['service_id'=>1,'method_id'=>2,'unit_id'=>3,'staff_id'=>4,'quantity'=>1,'date_time'=>'2026-11-12 09:00:00','duration_minutes'=>60,'timezone'=>'Australia/Melbourne'];if(CommitSelection::parse($s)!==$s)throw new Exception('selection');
echo "PASS\n";
