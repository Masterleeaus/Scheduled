<?php
declare(strict_types=1);
require __DIR__.'/pass33_bootstrap.php';
foreach (glob(__DIR__.'/../../core/app/Booking/Public/Redemption/*.php') as $f) require_once $f;
use App\Booking\Public\Redemption\{RedeemKey,RedeemClock,RedeemSelection,RedeemFingerprint,RedeemReceipt,RedeemTransition,RedeemPayload,RedeemBinding,RedeemSchema,RedeemLedger,RedeemReplay,RedeemCapabilities,RedeemOutcome,RedeemVerify,RedeemGuard,RedeemTransaction};
function ok(bool $result):void {if(!$result)throw new RuntimeException('Pass 34 assertion failed');}
function bad(callable $fn):void { $failed=false;try{$fn();}catch(Throwable $e){$failed=true;}ok($failed);}
$v=RedeemPayload::validate(['intent_token'=>str_repeat('a',80),'customer_email'=>'TEST@Example.com','customer_name'=>'  Jane  ']);ok($v['customer_name']==='Jane'&&$v['customer_email']==='test@example.com');bad(fn()=>RedeemPayload::validate(['intent_token'=>str_repeat('a',80),'customer_email'=>'x@example.com','customer_name'=>'Jane','payment_settled'=>true]));
echo 'PASS pass34_payload\n';
