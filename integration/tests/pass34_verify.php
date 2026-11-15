<?php
declare(strict_types=1);
require __DIR__.'/pass33_bootstrap.php';
foreach (glob(__DIR__.'/../../core/app/Booking/Public/Redemption/*.php') as $f) require_once $f;
use App\Booking\Public\Redemption\{RedeemKey,RedeemClock,RedeemSelection,RedeemFingerprint,RedeemReceipt,RedeemTransition,RedeemPayload,RedeemBinding,RedeemSchema,RedeemLedger,RedeemReplay,RedeemCapabilities,RedeemOutcome,RedeemVerify,RedeemGuard,RedeemTransaction};
function ok(bool $result):void {if(!$result)throw new RuntimeException('Pass 34 assertion failed');}
function bad(callable $fn):void { $failed=false;try{$fn();}catch(Throwable $e){$failed=true;}ok($failed);}
$secret=str_repeat('x',32);$tok=\App\Booking\Public\Intent\IntentIssuer::issue('example.com',4,fixture(),$secret,1000);$r=RedeemVerify::token($tok,'example.com',4,$secret,1001);ok(strlen($r['key'])===64&&count($r['selection'])===8);bad(fn()=>RedeemVerify::token($tok,'another.com',4,$secret,1001));
echo 'PASS pass34_verify\n';
