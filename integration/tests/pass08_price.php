<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPreviewRequest,BookingPreviewModifiers,BookingPreviewSettings,BookingPreviewPrice,BookingPreviewSlot,BookingPreviewIntersection,BookingPreviewSlotWindow,BookingPreviewDisplaySlots,BookingPreviewStaffPolicy,BookingPreviewSchedule,BookingPreviewResult,BookingPreviewPlanner,BookingPreviewRateAssumptions,BookingPreviewLimit,BookingPreviewAvailabilityQuery,BookingPreviewReadiness,BookingPreviewReader};
$r=['date'=>'2026-12-11','timezone'=>'UTC','service'=>['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'quantity'=>2],'staff_ids'=>[9,4],'duration_minutes'=>60,'step_minutes'=>30];$p=BookingPreviewRequest::parse($r);$settings=array_fill_keys(['tax_enabled','discount_enabled','coupon_enabled','addons_enabled','travel_fee_enabled','deposit_enabled'],false);$snap=['id'=>3,'service_id'=>1,'method_id'=>2,'min'=>1,'max'=>5,'base_cents'=>500];$q=BookingPreviewPrice::quote($p,$snap,[['rule'=>'E','units'=>2,'rate_cents'=>750]],[],$settings);if($q['amount_cents']!==1250||$q['payable']!==false)throw new RuntimeException('price');
echo 'PASS '.basename(__FILE__)."\n";
