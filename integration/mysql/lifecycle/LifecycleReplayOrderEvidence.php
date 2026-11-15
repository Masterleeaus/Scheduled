<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Idempotency replays must preserve order identity and immutable booking fields. */
final class LifecycleReplayOrderEvidence {
 public static function verify(array $before,array $after):void {
  LifecycleReplayEvidence::verify($before,$after);
  foreach(['id','order_id','client_id','service_id','method_id','method_unit_id','method_unit_qty','rate'] as $k){
   if(($before[$k]??null)!==($after[$k]??null))throw new \RuntimeException('Replay changed booking economics or identity');
  }
 }
}
