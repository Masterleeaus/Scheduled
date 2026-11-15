<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewPlanner {
 public static function build(array $input,array $snapshot,array $rates,array $staffSlots,array $settings):array {
  $request=BookingPreviewRequest::parse($input);
  $quote=BookingPreviewPrice::quote($request,$snapshot,$rates,$input,$settings);
  $slots=BookingPreviewSchedule::calculate($request,$staffSlots);
  return BookingPreviewResult::present($request,$quote,$slots);
 }
}
