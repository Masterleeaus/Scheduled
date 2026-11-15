<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** A validated webhook alone never implies confirmed booking or captured revenue. */
final class PaymentOutcome {
    public static function pending(): array {
        return ['accepted_for_reconciliation'=>false,'payment_settled'=>false,'booking_confirmed'=>false,'booking_writes_enabled'=>false];
    }
}
