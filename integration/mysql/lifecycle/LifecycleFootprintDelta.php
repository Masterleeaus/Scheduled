<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Exact changes, never approximate, to avoid accidental acceptance of extra writes. */
final class LifecycleFootprintDelta {
 public static function assert(array $before,array $after,int $bookings,int $orders):void {
  foreach(['bookings'=>$bookings,'orders'=>$orders] as $table=>$expected) {
   $old=$before[$table]??null;$new=$after[$table]??null;
   if(!is_int($old)||!is_int($new)||$old<0||$new<0||$expected<0)throw new \LogicException('Invalid footprint snapshot');
   if($old>PHP_INT_MAX-$expected||$new!==$old+$expected)throw new \RuntimeException('Unexpected '.$table.' database footprint');
  }
 }
}
