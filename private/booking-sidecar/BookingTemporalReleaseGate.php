<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Do not switch on writes without exercising DST and boundary tests. */
final class BookingTemporalReleaseGate {
 public static function evaluate(array $results):array {
  $checks=['dst_gap_rejection','dst_repeat_rejection','duration_elapsed','overlap_lookback','future_slot_check'];
  $missing=array_values(array_filter($checks,static fn($c)=>(($results[$c]??false)!==true)));
  return ['ready'=>$missing===[],'missing'=>$missing];
 }
}
