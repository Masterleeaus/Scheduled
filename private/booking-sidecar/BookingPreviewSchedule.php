<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewSchedule {
 public static function calculate(array $request,array $staffSlots):array {
  BookingPreviewStaffPolicy::assertCover($request['staff_ids'],$staffSlots);
  $common=BookingPreviewIntersection::all($staffSlots);
  $common=BookingPreviewSlotWindow::filter($common,$request['date'],$request['timezone'],$request['duration_minutes']);
  return BookingPreviewDisplaySlots::local($common,$request['timezone']);
 }
}
