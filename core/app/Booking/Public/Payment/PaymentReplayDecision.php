<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Receipt must be persisted under unique(provider,event_id) and unique(provider,payment_id) constraints. */
final class PaymentReplayDecision {
    public static function check(array $previous, array $incoming): string {
        if (!$previous) return 'new';
        foreach (['event_id','payment_id','booking_id','tenant_id','amount','currency'] as $key) {
            if (!array_key_exists($key, $previous) || !array_key_exists($key, $incoming) || $previous[$key] !== $incoming[$key]) throw new \DomainException('Conflicting payment replay');
        }
        return 'replay';
    }
}
