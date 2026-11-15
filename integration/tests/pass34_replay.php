<?php
declare(strict_types=1);
require __DIR__.'/pass33_bootstrap.php';
foreach (glob(__DIR__.'/../../core/app/Booking/Public/Redemption/*.php') as $f) require_once $f;
use App\Booking\Public\Redemption\{RedeemKey,RedeemClock,RedeemSelection,RedeemFingerprint,RedeemReceipt,RedeemTransition,RedeemPayload,RedeemBinding,RedeemSchema,RedeemLedger,RedeemReplay,RedeemCapabilities,RedeemOutcome,RedeemVerify,RedeemGuard,RedeemTransaction};
function ok(bool $result):void {if(!$result)throw new RuntimeException('Pass 34 assertion failed');}
function bad(callable $fn):void { $failed=false;try{$fn();}catch(Throwable $e){$failed=true;}ok($failed);}
$r=['owner_id'=>3,'fingerprint'=>'abc','state'=>'committed','booking_id'=>5];ok(RedeemReplay::interpret($r,3,'abc')['booking_id']===5);bad(fn()=>RedeemReplay::interpret($r,4,'abc'));bad(fn()=>RedeemReplay::interpret(['owner_id'=>3,'fingerprint'=>'abc','state'=>'committed','booking_id'=>0],3,'abc'));
echo 'PASS pass34_replay\n';
