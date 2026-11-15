<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicSlotFormat {
 public static function normalize(mixed $value,string $timezone):string {
  if(!is_string($value)||strlen($value)>50||!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/D',$value))throw new \RuntimeException('Invalid availability slot');
  $slot=\DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP',$value);
  if(!$slot||$slot->format('Y-m-d\TH:i:sP')!==$value)throw new \RuntimeException('Malformed availability slot');
  $local=$slot->setTimezone(new \DateTimeZone($timezone));
  if($local->format('Y-m-d')==='')throw new \RuntimeException('Invalid local slot');return $value;
 }
}
