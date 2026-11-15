<?php
declare(strict_types=1);
require_once __DIR__.'/../../app/Booking/Public/Payment/PaymentIdentity.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/PaymentMoney.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/StripeTestSignature.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/StripeBookingEvent.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/StripeBookingWebhookGuard.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/PaymentReceiptFingerprint.php';
require_once __DIR__.'/../../app/Booking/Public/Payment/PaymentEventTransition.php';
use App\Booking\Public\Payment\StripeBookingWebhookGuard as Guard;
use App\Booking\Public\Payment\PaymentReceiptFingerprint as Fingerprint;
use App\Booking\Public\Payment\PaymentEventTransition as Transition;
function check(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function rejection(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException $e){return;}throw new RuntimeException('Rejection expected');}
$secret='whsec_testing_some_nontrivial_secret';$now=time();
function payload(string $type,string $state,string $id='evt_ABCDEF123456',string $intent='pi_ABCDEF123456',int $amount=1200):string{
return json_encode(['id'=>$id,'type'=>$type,'livemode'=>false,'data'=>['object'=>['object'=>'payment_intent','id'=>$intent,'status'=>$state,'livemode'=>false,'currency'=>'aud','amount'=>$amount,'amount_received'=>$amount,'metadata'=>['booking_id'=>'23','tenant_id'=>'41']]]],JSON_THROW_ON_ERROR);
}
function signed(string $body,string $secret,int $now):string{return 't='.$now.',v1='.hash_hmac('sha256',$now.'.'.$body,$secret);}
$ok=payload('payment_intent.succeeded','succeeded');
$parsed=Guard::verify($ok,signed($ok,$secret,$now),$secret,$now);
check($parsed['kind']==='succeeded'&&$parsed['booking_id']===23&&$parsed['tenant_id']===41,'Success parse');
check(strlen(Fingerprint::make($parsed))===64,'Fingerprint');
check(Fingerprint::replay(Fingerprint::make($parsed),Fingerprint::make($parsed)),'Replay');
rejection(fn()=>Fingerprint::replay(Fingerprint::make($parsed),str_repeat('0',64)));
rejection(fn()=>Guard::verify($ok,'t='.$now.',v1='.str_repeat('0',64),$secret,$now));
rejection(fn()=>Guard::verify($ok,signed($ok,$secret,$now-400),$secret,$now));
$failed=payload('payment_intent.payment_failed','requires_payment_method','evt_FAILEDAAAA12');
check(Guard::verify($failed,signed($failed,$secret,$now),$secret,$now)['kind']==='failed','Failed payment');
rejection(fn()=>Guard::verify(payload('payment_intent.succeeded','requires_payment_method'),signed(payload('payment_intent.succeeded','requires_payment_method'),$secret,$now),$secret,$now));
check(Transition::observe('unseen','succeeded')==='succeeded_observed','Succeeded state');
check(Transition::observe('succeeded_observed','failed')==='succeeded_observed','Do not downgrade success');
check(Transition::observe('succeeded_observed','refunded')==='refunded_observed','Refund state');
check(Transition::observe('refunded_observed','failed')==='review_required','Conflicting state');
$refund=json_encode(['id'=>'evt_REFUND123456','type'=>'charge.refunded','livemode'=>false,'data'=>['object'=>['object'=>'charge','payment_intent'=>'pi_ABCDEF123456','livemode'=>false,'refunded'=>true,'amount_refunded'=>500,'currency'=>'aud']]],JSON_THROW_ON_ERROR);
$r=Guard::verify($refund,signed($refund,$secret,$now),$secret,$now);check($r['kind']==='refunded'&&$r['booking_id']===null,'Refund without guessed identity');
Guard::enabled(['enabled'=>true,'mode'=>'test','webhook_secret'=>$secret]);
rejection(fn()=>Guard::enabled(['enabled'=>false,'mode'=>'test','webhook_secret'=>$secret]));
echo "Pass 12 Stripe inbox: 13 checks passed\n";
