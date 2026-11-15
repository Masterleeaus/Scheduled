<?php
declare(strict_types=1);
require __DIR__.'/pass33_bootstrap.php';
foreach (glob(__DIR__.'/../../core/app/Booking/Public/Redemption/*.php') as $f) require_once $f;
use App\Booking\Public\Redemption\{RedeemKey,RedeemClock,RedeemSelection,RedeemFingerprint,RedeemReceipt,RedeemTransition,RedeemPayload,RedeemBinding,RedeemSchema,RedeemLedger,RedeemReplay,RedeemCapabilities,RedeemOutcome,RedeemVerify,RedeemGuard,RedeemTransaction};
function ok(bool $result):void {if(!$result)throw new RuntimeException('Pass 34 assertion failed');}
function bad(callable $fn):void { $failed=false;try{$fn();}catch(Throwable $e){$failed=true;}ok($failed);}
$d=['host'=>'example.com','owner'=>4,'scope'=>'preview_only'];RedeemBinding::require($d,'example.com',4);bad(fn()=>RedeemBinding::require($d,'example.com',5));
echo 'PASS pass34_binding\n';
