<?php
declare(strict_types=1);
spl_autoload_register(function($name){$prefix="App\\Booking\\Public\\Redemption\\";if(str_starts_with($name,$prefix)){require __DIR__."/../../core/app/Booking/Public/Redemption/".substr($name,strlen($prefix)).".php";}});
use App\Booking\Public\Redemption\{CommitBookingId,CommitConnection,CommitIntentScope,CommitEvidence,CommitEvidenceReader,CommitLedgerState,CommitReleaseGate,CommitResponse,CommitError,CommitReplayCheck,CommitCallback,CommitTransactionBoundary,CommitSameDatabase,CommitSelection,CommitCustomer,CommitOrchestrator};
$p=CommitCustomer::parse(['intent_token'=>str_repeat('a',80),'customer_email'=>'TEST@EXAMPLE.COM','customer_name'=>' A ']);if($p['customer_email']!=='test@example.com'||$p['customer_name']!=='A')throw new Exception('customer');
echo "PASS\n";
