<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;

/** Parses the only Stripe events admitted to the isolated booking payment inbox. */
final class StripeBookingEvent {
    private const ALLOWED = [
        'payment_intent.succeeded'=>'succeeded',
        'payment_intent.payment_failed'=>'failed',
        'charge.refunded'=>'refunded',
    ];
    public static function parse(string $raw): array {
        try { $event=json_decode($raw,true,32,JSON_THROW_ON_ERROR); }
        catch(\JsonException $e) { throw new \InvalidArgumentException('Malformed payment event',0,$e); }
        if (!is_array($event) || array_is_list($event) || !is_string($event['id']??null) ||
            !preg_match('/^evt_[A-Za-z0-9]{6,120}$/D',$event['id']) ||
            !array_key_exists($event['type']??'',self::ALLOWED) || ($event['livemode']??null)!==false) {
            throw new \InvalidArgumentException('Unsupported test payment event');
        }
        $kind=self::ALLOWED[$event['type']];
        $obj=$event['data']['object']??null;
        if(!is_array($obj)||array_is_list($obj)||($obj['livemode']??null)!==false)throw new \InvalidArgumentException('Invalid payment object');
        if($kind==='refunded') {
            if(($obj['object']??null)!=='charge'||!is_string($obj['payment_intent']??null)||!preg_match('/^pi_[A-Za-z0-9]{6,120}$/D',$obj['payment_intent']))throw new \InvalidArgumentException('Invalid refund reference');
            $payment=$obj['payment_intent'];$money=$obj['amount_refunded']??null;
            if(($obj['refunded']??null)!==true)throw new \InvalidArgumentException('Refund unverified');
        } else {
            if(($obj['object']??null)!=='payment_intent'||!is_string($obj['id']??null)||!preg_match('/^pi_[A-Za-z0-9]{6,120}$/D',$obj['id']))throw new \InvalidArgumentException('Invalid intent');
            if(($kind==='succeeded'&&($obj['status']??null)!=='succeeded')||($kind==='failed'&&($obj['status']??null)!=='requires_payment_method'))throw new \InvalidArgumentException('Event state mismatch');
            $payment=$obj['id'];$money=$kind==='succeeded'?($obj['amount_received']??null):($obj['amount']??null);
        }
        $metadata=$obj['metadata']??null;
        // Refund charge metadata is not guaranteed to inherit PaymentIntent metadata;
        // do not fabricate tenant or booking identity. Such events are held for review.
        $booking=null;$tenant=null;
        if(is_array($metadata)&&isset($metadata['booking_id'],$metadata['tenant_id'])){
            $booking=PaymentIdentity::positiveId($metadata['booking_id']);
            $tenant=PaymentIdentity::positiveId($metadata['tenant_id']);
        } elseif($kind!=='refunded')throw new \InvalidArgumentException('Missing booking metadata');
        return [
            'provider'=>'stripe','event_id'=>$event['id'],'payment_id'=>$payment,
            'kind'=>$kind,'booking_id'=>$booking,'tenant_id'=>$tenant,
            'amount_minor'=>PaymentMoney::minor($money),
            'currency'=>PaymentMoney::currency($obj['currency']??null),
            'payload_hash'=>hash('sha256',$raw),
        ];
    }
}
