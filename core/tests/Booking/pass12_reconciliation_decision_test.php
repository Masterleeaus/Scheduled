<?php
declare(strict_types=1);
require_once __DIR__.'/../../app/Booking/Public/Payment/PaymentIdentity.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/PaymentMoney.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/PaymentReconciliationDecision.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/PaymentEventMetrics.php';
use App\Booking\Public\Payment\PaymentReconciliationDecision as D;
use App\Booking\Public\Payment\PaymentEventMetrics as M;
$event=['kind'=>'succeeded','provider'=>'stripe','payment_id'=>'pi_123abc','amount_minor'=>1500,'currency'=>'aud','booking_id'=>23,'tenant_id'=>41];
$provider=['provider'=>'stripe','payment_id'=>'pi_123abc','livemode'=>false,'status'=>'succeeded','amount_received_minor'=>1500,'currency'=>'aud'];
$booking=['id'=>23,'tenant_id'=>41,'status'=>'pending_payment','amount_due_minor'=>1500,'currency'=>'aud'];
function assert12(bool $condition):void{if(!$condition)throw new RuntimeException('Reconciliation check failed');}
$x=D::evaluate($event,$provider,$booking);assert12($x['eligible']===true&&$x['payment_settled']===false&&$x['booking_confirmed']===false);
foreach(['payment_id'=>'pi_bad','status'=>'requires_payment_method','currency'=>'usd','amount_received_minor'=>1000,'livemode'=>true] as $key=>$value){$bad=$provider;$bad[$key]=$value;assert12(D::evaluate($event,$bad,$booking)['eligible']===false);}
foreach(['id'=>22,'tenant_id'=>42,'status'=>'cancelled','amount_due_minor'=>100] as $key=>$value){$bad=$booking;$bad[$key]=$value;assert12(D::evaluate($event,$provider,$bad)['eligible']===false);}
$bad=$event;$bad['kind']='refunded';assert12(D::evaluate($bad,$provider,$booking)['eligible']===false);
$m=M::summarize([['state'=>'received','total'=>2],['state'=>'awaiting_reconciliation','total'=>4]]);assert12($m['outstanding']===6 && $m['complete']===0);
echo 'Pass 12 reconciliation: 12 checks passed'.PHP_EOL;
