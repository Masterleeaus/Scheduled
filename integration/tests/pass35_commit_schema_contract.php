<?php
declare(strict_types=1);
spl_autoload_register(function($name){$prefix="App\\Booking\\Public\\Redemption\\";if(str_starts_with($name,$prefix)){require __DIR__."/../../core/app/Booking/Public/Redemption/".substr($name,strlen($prefix)).".php";}});
use App\Booking\Public\Redemption\{CommitBookingId,CommitConnection,CommitIntentScope,CommitEvidence,CommitEvidenceReader,CommitLedgerState,CommitReleaseGate,CommitResponse,CommitError,CommitReplayCheck,CommitCallback,CommitTransactionBoundary,CommitSameDatabase,CommitSelection,CommitCustomer,CommitOrchestrator};
$sql=\App\Booking\Public\Redemption\RedeemSchema::ddl();if(str_contains($sql,'ENGINE=')||!str_contains($sql,'claim_key'))throw new Exception('schema');
echo "PASS\n";
