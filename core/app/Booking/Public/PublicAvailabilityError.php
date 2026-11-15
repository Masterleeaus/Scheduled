<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicAvailabilityError {
 public static function status(\Throwable $e):int {return $e instanceof \InvalidArgumentException?422:404;}
 public static function message(\Throwable $e):string {return $e instanceof \InvalidArgumentException?'Invalid availability request':'Availability currently unavailable';}
}
