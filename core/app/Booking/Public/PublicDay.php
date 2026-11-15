<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicDay {
 public static function parse(mixed $input,string $timezone):string {
  if(!is_string($input)||!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$input))throw new \InvalidArgumentException('Invalid date');
  $zone=new \DateTimeZone($timezone);$day=\DateTimeImmutable::createFromFormat('!Y-m-d',$input,$zone);
  if(!$day||$day->format('Y-m-d')!==$input)throw new \InvalidArgumentException('Invalid calendar date');
  $today=new \DateTimeImmutable('today',$zone);
  if($day<$today||$day>$today->modify('+180 days'))throw new \InvalidArgumentException('Date outside scheduling horizon');return $input;
 }
}
