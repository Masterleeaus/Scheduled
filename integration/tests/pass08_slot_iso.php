<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPreviewRequest,BookingPreviewModifiers,BookingPreviewSettings,BookingPreviewPrice,BookingPreviewSlot,BookingPreviewIntersection,BookingPreviewSlotWindow,BookingPreviewDisplaySlots,BookingPreviewStaffPolicy,BookingPreviewSchedule,BookingPreviewResult,BookingPreviewPlanner,BookingPreviewRateAssumptions,BookingPreviewLimit,BookingPreviewAvailabilityQuery,BookingPreviewReadiness,BookingPreviewReader};
$s=BookingPreviewSlot::timestamp('2026-12-11T09:00:00+00:00');if($s!==1796979600)throw new RuntimeException('ISO parse');
echo 'PASS '.basename(__FILE__)."\n";
