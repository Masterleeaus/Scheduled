<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
/** Supply a tenant-scoped persisted read callback; never resolve booking by customer-provided ID alone. */
final class CustomerBookingLookup {
    public static function read(string $token,int $tenant,int $now,string $secret,callable $reader):array {
        $booking=CustomerBookingGrant::verify($token,$tenant,$now,$secret);
        $row=$reader($tenant,$booking);
        if (!is_array($row)) throw new \DomainException('Booking not found');
        return CustomerBookingProjection::fromPersisted($row,$tenant,$booking);
    }
}
