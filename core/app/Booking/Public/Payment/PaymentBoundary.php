<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Public booking payment cannot settle until an authenticated, persisted booking exists. */
final class PaymentBoundary {
    public static function assertEnabled(array $settings): void {
        if (($settings['environment'] ?? '') !== 'test' || ($settings['booking_write_gate'] ?? false) !== true || ($settings['db_acceptance_passed'] ?? false) !== true || ($settings['payment_test_gate'] ?? false) !== true) {
            throw new \DomainException('Payment settlement unavailable');
        }
    }
}
