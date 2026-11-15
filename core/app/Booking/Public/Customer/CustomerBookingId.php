<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
final class CustomerBookingId {
    public static function parse(mixed $value): int {
        if (is_int($value) && $value > 0 && $value <= 2147483647) return $value;
        if (!is_string($value) || !preg_match('/^[1-9][0-9]{0,9}$/D', $value)) throw new \InvalidArgumentException('Invalid booking reference');
        $n=(int)$value;
        if ($n < 1 || $n > 2147483647) throw new \InvalidArgumentException('Invalid booking reference');
        return $n;
    }
}
