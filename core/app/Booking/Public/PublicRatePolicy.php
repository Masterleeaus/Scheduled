<?php
namespace App\Booking\Public;
final class PublicRatePolicy {
 public const LIMIT_PER_MINUTE=20;
 public static function acceptable(int $count):bool{return $count>=0&&$count<=self::LIMIT_PER_MINUTE;}
}
