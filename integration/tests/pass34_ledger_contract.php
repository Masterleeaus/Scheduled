<?php
declare(strict_types=1);
require __DIR__.'/pass33_bootstrap.php';
foreach (glob(__DIR__.'/../../core/app/Booking/Public/Redemption/*.php') as $f) require_once $f;
use App\Booking\Public\Redemption\{RedeemKey,RedeemClock,RedeemSelection,RedeemFingerprint,RedeemReceipt,RedeemTransition,RedeemPayload,RedeemBinding,RedeemSchema,RedeemLedger,RedeemReplay,RedeemCapabilities,RedeemOutcome,RedeemVerify,RedeemGuard,RedeemTransaction};
function ok(bool $result):void {if(!$result)throw new RuntimeException('Pass 34 assertion failed');}
function bad(callable $fn):void { $failed=false;try{$fn();}catch(Throwable $e){$failed=true;}ok($failed);}
$src=file_get_contents(__DIR__.'/../../core/app/Booking/Public/Redemption/RedeemLedger.php');ok(str_contains($src,'ON CONFLICT (claim_key) DO NOTHING'));ok(!str_contains($src,'INSERT IGNORE'));ok(str_contains($src,'FOR UPDATE'));ok(str_contains($src,"state='claimed'"));
echo 'PASS pass34_ledger_contract\n';
