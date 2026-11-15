<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
/** Issue read capability only after authoritative tenant-scoped booking readback. */
final class CustomerBookingGrantIssuer {
    public static function afterReadback(int $tenant,int $booking,int $now,string $secret,callable $reader):string {
        $row=$reader(CustomerBookingId::parse($tenant),CustomerBookingId::parse($booking));
        if (!is_array($row)) throw new \DomainException('Booking not persisted');
        CustomerBookingEvidence::requireRow($row,$tenant,$booking);
        return CustomerBookingGrant::issue($tenant,$booking,$secret,$now);
    }
}
