<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Use integer minor units; never accept binary-float amounts. */
final class PaymentMoney {
    public static function minor(mixed $value): int {
        if (!is_int($value) || $value < 1 || $value > 100000000) throw new \InvalidArgumentException('Invalid minor-unit amount');
        return $value;
    }
    public static function currency(mixed $value): string {
        if (!is_string($value) || !preg_match('/^[a-z]{3}$/D', $value)) throw new \InvalidArgumentException('Invalid currency');
        return $value;
    }
}
