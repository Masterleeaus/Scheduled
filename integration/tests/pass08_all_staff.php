<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPreviewRequest,BookingPreviewModifiers,BookingPreviewSettings,BookingPreviewPrice,BookingPreviewSlot,BookingPreviewIntersection,BookingPreviewSlotWindow,BookingPreviewDisplaySlots,BookingPreviewStaffPolicy,BookingPreviewSchedule,BookingPreviewResult,BookingPreviewPlanner,BookingPreviewRateAssumptions,BookingPreviewLimit,BookingPreviewAvailabilityQuery,BookingPreviewReadiness,BookingPreviewReader};
$r=['date'=>'2026-12-11','timezone'=>'UTC','service'=>['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'quantity'=>2],'staff_ids'=>[9,4],'duration_minutes'=>60,'step_minutes'=>30];$p=BookingPreviewRequest::parse($r);$slots=[4=>['2026-12-11T09:00:00+00:00','2026-12-11T09:30:00+00:00'],9=>['2026-12-11T09:30:00+00:00']];$v=BookingPreviewSchedule::calculate($p,$slots);if(count($v)!==1||$v[0]!=='2026-12-11 09:30:00+00:00')throw new RuntimeException('slots');
echo 'PASS '.basename(__FILE__)."\n";
