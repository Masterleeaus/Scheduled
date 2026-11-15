<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
final class CustomerBookingHeaders {
    public static function all():array {
        return ['Cache-Control'=>'no-store, private','Pragma'=>'no-cache','X-Content-Type-Options'=>'nosniff','Referrer-Policy'=>'no-referrer'];
    }
}
