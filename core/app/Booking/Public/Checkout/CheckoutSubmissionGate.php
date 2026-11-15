<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;
/** Independent server-controlled boundary. Never take readiness, prices or booking authority from JSON. */
final class CheckoutSubmissionGate {
    public static function dispatch(bool $enabled, $executor, int $owner, array $selection, array $customer, string $nonce): array {
        if (!$enabled || !($executor instanceof CheckoutSubmissionExecutor)) {
            throw new \RuntimeException('Public booking submission unavailable');
        }
        if ($owner < 1 || !preg_match('/^[a-f0-9]{32,128}$/D', $nonce)) {
            throw new \InvalidArgumentException('Invalid trusted booking identity');
        }
        $mapped = CheckoutNativeCustomer::map($customer);
        CheckoutCommitCompatibility::assert($selection, $customer, $nonce);
        $result = $executor->commit($owner, $selection, $mapped['identity'], $mapped['personal_info'], $nonce);
        if (!is_array($result) || ($result['confirmed'] ?? null) !== true ||
            ($result['payment_settled'] ?? null) !== false ||
            !is_int($result['booking_id'] ?? null) || $result['booking_id'] <= 0 ||
            !is_bool($result['replay'] ?? null)) {
            throw new \RuntimeException('Booking persistence proof unavailable');
        }
        return ['confirmed'=>true,'payment_settled'=>false,'booking_reference'=>(string)$result['booking_id'],
            'replay'=>$result['replay']];
    }
}
