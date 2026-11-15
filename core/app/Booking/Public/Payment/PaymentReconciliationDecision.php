<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Fail-closed evaluation of independent provider readback and booking evidence. */
final class PaymentReconciliationDecision {
    public static function evaluate(array $event,array $provider,array $booking): array {
        $reject=['eligible'=>false,'payment_settled'=>false,'booking_confirmed'=>false,'reason'=>'review_required'];
        if(($event['kind']??null)!=='succeeded'||($event['provider']??null)!=='stripe')return $reject;
        foreach(['payment_id','amount_minor','currency'] as $key)if(!isset($event[$key]))return $reject;
        foreach(['payment_id','amount_received_minor','currency'] as $key)if(!array_key_exists($key,$provider))return $reject;
        try {
            if(!hash_equals((string)$event['payment_id'],(string)$provider['payment_id']) ||
                ($provider['provider']??null)!=='stripe' || ($provider['livemode']??null)!==false ||
                ($provider['status']??null)!=='succeeded' ||
                PaymentMoney::minor($provider['amount_received_minor']??null)!==PaymentMoney::minor($event['amount_minor']) ||
                PaymentMoney::currency($provider['currency']??null)!==PaymentMoney::currency($event['currency']) ||
                PaymentIdentity::positiveId($booking['id']??null)!==PaymentIdentity::positiveId($event['booking_id']??null) ||
                PaymentIdentity::positiveId($booking['tenant_id']??null)!==PaymentIdentity::positiveId($event['tenant_id']??null) ||
                PaymentMoney::minor($booking['amount_due_minor']??null)!==PaymentMoney::minor($event['amount_minor']) ||
                PaymentMoney::currency($booking['currency']??null)!==PaymentMoney::currency($event['currency']) ||
                !in_array($booking['status']??null,['pending_payment','payment_processing'],true))return $reject;
        }catch(\InvalidArgumentException $e){return $reject;}
        return ['eligible'=>true,'payment_settled'=>false,'booking_confirmed'=>false,'reason'=>'independent_match_requires_authorised_settlement_commit'];
    }
}
