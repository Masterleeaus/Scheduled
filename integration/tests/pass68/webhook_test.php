<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use App\Booking\Public\Payment\{PaymentBoundary,PaymentMoney,PaymentIdentity,StripeTestSignature,StripeTestEvent,PaymentBookingMatch,PaymentReplayDecision,PaymentWebhookCandidate,PaymentOutcome};
$count=0; $pass=function(bool $v,string $label)use(&$count){assert68($v,$label);$count++;};
$bad=function(callable $fn,string $label)use(&$count){reject68($fn,$label);$count++;};
$secret='whsec_'.str_repeat('a',32);$now=1800000000;
$event=['id'=>'evt_abc12345','livemode'=>false,'type'=>'payment_intent.succeeded','data'=>['object'=>['id'=>'pi_abc12345','object'=>'payment_intent','status'=>'succeeded','livemode'=>false,'amount_received'=>8000,'currency'=>'aud','metadata'=>['booking_id'=>'21','tenant_id'=>'7']]]];
$body=json_encode($event,JSON_THROW_ON_ERROR);$header='t='.$now.',v1='.hash_hmac('sha256',$now.'.'.$body,$secret);
$candidate=PaymentWebhookCandidate::fromStripeTest($body,$header,$secret,$now);
$pass($candidate['booking_id']===21 && $candidate['amount']===8000,'signed candidate');
$pass($candidate['tenant_id']===7 && $candidate['currency']==='aud','tenant currency');
$bad(fn()=>StripeTestSignature::verify($body.' ', $header,$secret,$now),'modified raw body');
$bad(fn()=>StripeTestSignature::verify($body, $header,$secret,$now+301),'stale event');
$bad(fn()=>StripeTestSignature::verify($body, $header,'whsec_'.str_repeat('b',32),$now),'wrong secret');
$bad(fn()=>StripeTestSignature::verify($body, 't=1800000000,t=1800000000,v1='.hash_hmac('sha256',$now.'.'.$body,$secret),$secret,$now),'duplicate timestamp');
$bad(fn()=>StripeTestSignature::verify($body, 't='.$now.',v1=abc',$secret,$now),'malformed signature');
$bad(fn()=>StripeTestSignature::verify(str_repeat('x',1048577),$header,$secret,$now),'body limit');
$bad(fn()=>PaymentBoundary::assertEnabled(['environment'=>'production','booking_write_gate'=>true,'db_acceptance_passed'=>true,'payment_test_gate'=>true]),'production gate');
$bad(fn()=>PaymentBoundary::assertEnabled(['environment'=>'test','booking_write_gate'=>false,'db_acceptance_passed'=>true,'payment_test_gate'=>true]),'write gate');
$bad(fn()=>PaymentBoundary::assertEnabled(['environment'=>'test','booking_write_gate'=>true,'db_acceptance_passed'=>false,'payment_test_gate'=>true]),'db gate');
PaymentBoundary::assertEnabled(['environment'=>'test','booking_write_gate'=>true,'db_acceptance_passed'=>true,'payment_test_gate'=>true]);$pass(true,'staging gates');
$booking=['id'=>21,'tenant_id'=>7,'amount_due_minor'=>8000,'currency'=>'aud','status'=>'pending_payment'];PaymentBookingMatch::assertMatch($candidate,$booking);$pass(true,'matching booking');
foreach(['id'=>22,'tenant_id'=>8,'amount_due_minor'=>8001,'currency'=>'usd','status'=>'cancelled'] as $k=>$v) { $copy=$booking;$copy[$k]=$v;$bad(fn()=>PaymentBookingMatch::assertMatch($candidate,$copy),'booking mismatch '.$k); }
$pass(PaymentReplayDecision::check([],$candidate)==='new','new receipt');
$pass(PaymentReplayDecision::check($candidate,$candidate)==='replay','identical replay');
$altered=$candidate;$altered['amount']=1;$bad(fn()=>PaymentReplayDecision::check($candidate,$altered),'conflicting replay');
foreach (['livemode'=>true,'type'=>'charge.succeeded'] as $k=>$v){$copy=$event;$copy[$k]=$v;$bad(fn()=>StripeTestEvent::parse(json_encode($copy)),'event '.$k);}
$copy=$event;$copy['data']['object']['livemode']=true;$bad(fn()=>StripeTestEvent::parse(json_encode($copy)),'live intent');
$copy=$event;$copy['data']['object']['status']='requires_payment_method';$bad(fn()=>StripeTestEvent::parse(json_encode($copy)),'incomplete payment');
$copy=$event;$copy['data']['object']['amount_received']=80.0;$bad(fn()=>StripeTestEvent::parse(json_encode($copy,JSON_PRESERVE_ZERO_FRACTION)),'floating amount');
$copy=$event;$copy['data']['object']['metadata']['tenant_id']='07';$bad(fn()=>StripeTestEvent::parse(json_encode($copy)),'noncanonical tenant');
$bad(fn()=>PaymentIdentity::positiveId('2147483648'),'identity overflow');
$bad(fn()=>PaymentMoney::currency('AUD'),'uppercase currency');
$pass(PaymentOutcome::pending()['booking_confirmed']===false,'never confirm');
echo "Pass 68 webhook boundary assertions: $count passed\n";
