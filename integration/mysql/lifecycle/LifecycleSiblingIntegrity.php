<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Failed test if mutating a booking changes any other booking on its order. */
final class LifecycleSiblingIntegrity {
 public static function verify(array $before,array $after):void {
  if(array_keys($before)!==array_keys($after))throw new \RuntimeException('Sibling bookings added or removed during mutation');
  foreach($before as $id=>$record)if($record!==$after[$id])throw new \RuntimeException('Sibling booking changed during mutation');
 }
}
