<?php
declare(strict_types=1);
spl_autoload_register(function($name){$prefix="App\\Booking\\Public\\Redemption\\";if(str_starts_with($name,$prefix)){require __DIR__."/../../core/app/Booking/Public/Redemption/".substr($name,strlen($prefix)).".php";}});
use App\Booking\Public\Redemption\{CommitBookingId,CommitConnection,CommitIntentScope,CommitEvidence,CommitEvidenceReader,CommitLedgerState,CommitReleaseGate,CommitResponse,CommitError,CommitReplayCheck,CommitCallback,CommitTransactionBoundary,CommitSameDatabase,CommitSelection,CommitCustomer,CommitOrchestrator};
CommitEvidence::assert(['id'=>4,'booking_status'=>'A','client_id'=>9,'booking_date_time'=>'2026-11-12 10:00:00'],4);
echo "PASS\n";
