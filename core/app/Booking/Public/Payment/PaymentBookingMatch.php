<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Authoritative booking/order readback, not client-submitted pricing. */
final class PaymentBookingMatch {
    public static function assertMatch(array $event, array $persistedBooking): void {
        if (PaymentIdentity::positiveId($persistedBooking['id'] ?? null) !== $event['booking_id'] ||
            PaymentIdentity::positiveId($persistedBooking['tenant_id'] ?? null) !== $event['tenant_id'] ||
            PaymentMoney::minor($persistedBooking['amount_due_minor'] ?? null) !== $event['amount'] ||
            PaymentMoney::currency($persistedBooking['currency'] ?? null) !== $event['currency'] ||
            !in_array($persistedBooking['status'] ?? null, ['pending_payment', 'payment_processing'], true)) {
            throw new \DomainException('Payment does not match pending booking');
        }
    }
}
