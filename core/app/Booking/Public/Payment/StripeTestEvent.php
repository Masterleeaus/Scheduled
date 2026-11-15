<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Conservative parsing. Only a successful test-mode PaymentIntent is eligible for reconciliation. */
final class StripeTestEvent {
    public static function parse(string $raw): array {
        try { $event = json_decode($raw, true, 32, JSON_THROW_ON_ERROR); } catch (\JsonException $e) { throw new \InvalidArgumentException('Invalid event JSON', 0, $e); }
        if (!is_array($event) || array_is_list($event) || !is_string($event['id'] ?? null) || !preg_match('/^evt_[A-Za-z0-9]{6,120}$/D', $event['id']) || ($event['livemode'] ?? null) !== false || ($event['type'] ?? null) !== 'payment_intent.succeeded') throw new \InvalidArgumentException('Unsupported test payment event');
        $obj = $event['data']['object'] ?? null;
        if (!is_array($obj) || ($obj['object'] ?? null) !== 'payment_intent' || !is_string($obj['id'] ?? null) || !preg_match('/^pi_[A-Za-z0-9]{6,120}$/D', $obj['id']) || ($obj['status'] ?? null) !== 'succeeded' || ($obj['livemode'] ?? null) !== false) throw new \InvalidArgumentException('Invalid payment intent');
        $metadata = $obj['metadata'] ?? null;
        if (!is_array($metadata) || !isset($metadata['booking_id'], $metadata['tenant_id'])) throw new \InvalidArgumentException('Missing booking metadata');
        $bookingId = PaymentIdentity::positiveId($metadata['booking_id']);
        $tenantId = PaymentIdentity::positiveId($metadata['tenant_id']);
        return ['event_id'=>$event['id'], 'payment_id'=>$obj['id'], 'booking_id'=>$bookingId, 'tenant_id'=>$tenantId,
            'amount'=>PaymentMoney::minor($obj['amount_received'] ?? null), 'currency'=>PaymentMoney::currency($obj['currency'] ?? null)];
    }
}
