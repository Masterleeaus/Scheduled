<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Strict command adapted from the existing Cleanto booking input contract. */
final class BookingRescheduleCommand {
 public static function parse(array $input):array {
  $c=BookingReschedule::parse($input);
  $reason=$input['reason']??'';
  if(!is_string($reason)||strlen($reason)>200||preg_match('/[\x00-\x1f]/',$reason))throw new \InvalidArgumentException('Invalid reason');
  if(($c['window']['end']-$c['window']['start'])<900)throw new \InvalidArgumentException('Invalid window');
  $c['reason']=$reason;$c['timezone']=$input['timezone'];return $c;
 }
}
