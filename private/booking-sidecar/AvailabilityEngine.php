<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pure slot computation from an already tenant-scoped schedule snapshot. */
final class AvailabilityEngine {
 public static function calculate(array $request,array $shifts,array $breaks,array $offtimes,array $dayoffs,array $busy): array {
  $p=AvailabilityPayload::parse($request);$day=Interval::parse($p['date'].' 00:00:00',$p['timezone']);
  if (DayOffPolicy::blocked($dayoffs)) return [];
  $working=StaffShiftWindow::forDay($shifts,$day,$p['timezone']);
  if (!$working) return [];
  $blocked=array_merge(BreakWindow::forDay($breaks,$day,$p['timezone'],$shifts),TimeOffWindow::fromRows($offtimes,$p['timezone']),BookingBusyWindow::fromRecords($busy,$p['timezone']));
  return SlotGrid::generate(IntervalSet::subtract($working,$blocked),$day,$p['duration_minutes'],$p['step_minutes']);
 }
}
