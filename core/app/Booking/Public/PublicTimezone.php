<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicTimezone {
 public static function parse(mixed $input):string {
  if(!is_string($input)||strlen($input)>64||!in_array($input,\DateTimeZone::listIdentifiers(),true))throw new \InvalidArgumentException('Invalid timezone');return $input;
 }
}
