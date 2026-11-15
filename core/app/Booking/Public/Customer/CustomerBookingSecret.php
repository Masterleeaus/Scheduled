<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
final class CustomerBookingSecret {
    public static function require(mixed $secret): string {
        if (!is_string($secret) || strlen($secret) < 32 || strlen($secret) > 4096) throw new \RuntimeException('Customer booking access secret unavailable');
        return $secret;
    }
}
