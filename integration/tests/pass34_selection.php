<?php
declare(strict_types=1);
require __DIR__.'/pass33_bootstrap.php';
foreach (glob(__DIR__.'/../../core/app/Booking/Public/Redemption/*.php') as $f) require_once $f;
use App\Booking\Public\Redemption\{RedeemKey,RedeemClock,RedeemSelection,RedeemFingerprint,RedeemReceipt,RedeemTransition,RedeemPayload,RedeemBinding,RedeemSchema,RedeemLedger,RedeemReplay,RedeemCapabilities,RedeemOutcome,RedeemVerify,RedeemGuard,RedeemTransaction};
function ok(bool $result):void {if(!$result)throw new RuntimeException('Pass 34 assertion failed');}
function bad(callable $fn):void { $failed=false;try{$fn();}catch(Throwable $e){$failed=true;}ok($failed);}
$s=RedeemSelection::canonical(fixture());ok(count($s)===8);$s=fixture();$s['tenant_id']=8;bad(fn()=>RedeemSelection::canonical($s));
echo 'PASS pass34_selection\n';
