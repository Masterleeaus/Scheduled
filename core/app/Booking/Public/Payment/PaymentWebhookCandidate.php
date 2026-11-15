<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Returns a verified reconciliation candidate; does NOT mutate DB or confirm booking. */
final class PaymentWebhookCandidate {
    public static function fromStripeTest(string $body, string $signatureHeader, string $webhookSecret, int $now): array {
        StripeTestSignature::verify($body, $signatureHeader, $webhookSecret, $now);
        return StripeTestEvent::parse($body);
    }
}
