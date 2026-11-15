<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
final class PaymentIdentity {
    public static function positiveId(mixed $id): int {
        if (is_int($id) && $id > 0 && $id <= 2147483647) return $id;
        if (!is_string($id) || !preg_match('/^[1-9][0-9]{0,9}$/D', $id)) throw new \InvalidArgumentException('Invalid booking identity');
        $number = (int)$id;
        if ($number < 1 || $number > 2147483647) throw new \InvalidArgumentException('Invalid booking identity');
        return $number;
    }
}
