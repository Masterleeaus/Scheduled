<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Verify persisted Cleanto service/method/quantity against BookingPriceInput's canonical `quantity`. */
final class LifecycleServiceEvidence {
 public static function verify(array $booking,array $create):void {
  $service=$create['service']??null;
  if(!is_array($service))throw new \RuntimeException('Invalid service fixture');
  foreach(['service_id','method_id','method_unit_id'] as $key){
   $expected=self::positive($service[$key]??null,$key);
   if(($booking[$key]??null)!==$expected)throw new \RuntimeException('Stored service mapping mismatch: '.$key);
  }
  $canonical=$service['quantity']??null;
  $legacy=$service['method_unit_qty']??null;
  if($canonical===null&&$legacy===null)throw new \RuntimeException('Missing service quantity');
  if($canonical!==null&&$legacy!==null&&self::positive($canonical,'quantity')!==self::positive($legacy,'method_unit_qty'))
   throw new \RuntimeException('Conflicting service quantities');
  $expected=self::positive($canonical??$legacy,'quantity');
  if(($booking['method_unit_qty']??null)!==$expected)throw new \RuntimeException('Stored service mapping mismatch: method_unit_qty');
 }
 private static function positive(mixed $value,string $field):int {
  if(!is_int($value)&&!(is_string($value)&&preg_match('/^[1-9][0-9]*$/D',$value)))throw new \RuntimeException('Invalid service field: '.$field);
  $value=(int)$value;
  if($value<1||$value>10000)throw new \RuntimeException('Out-of-range service field: '.$field);
  return $value;
 }
}
