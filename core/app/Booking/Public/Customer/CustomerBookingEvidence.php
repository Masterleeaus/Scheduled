<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
final class CustomerBookingEvidence {
    public static function requireRow(array $row, int $tenant, int $booking): array {
        if (CustomerBookingId::parse($row['id']??null)!==CustomerBookingId::parse($booking) ||
            CustomerBookingId::parse($row['tenant_id']??null)!==CustomerBookingId::parse($tenant)) {
            throw new \DomainException('Booking not found');
        }
        if (!is_string($row['reference']??null) || !preg_match('/^[A-Za-z0-9-]{6,64}$/D',$row['reference'])) throw new \DomainException('Booking reference unavailable');
        $status=CustomerBookingStatus::interpret($row['status']??null);
        return ['reference'=>$row['reference']]+$status;
    }
}
