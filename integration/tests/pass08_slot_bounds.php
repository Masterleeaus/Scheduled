<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPreviewRequest,BookingPreviewModifiers,BookingPreviewSettings,BookingPreviewPrice,BookingPreviewSlot,BookingPreviewIntersection,BookingPreviewSlotWindow,BookingPreviewDisplaySlots,BookingPreviewStaffPolicy,BookingPreviewSchedule,BookingPreviewResult,BookingPreviewPlanner,BookingPreviewRateAssumptions,BookingPreviewLimit,BookingPreviewAvailabilityQuery,BookingPreviewReadiness,BookingPreviewReader};
$x=BookingPreviewSlotWindow::filter([1796979600],'2026-12-11','UTC',60);if($x!==[1796979600])throw new RuntimeException('window');try{BookingPreviewSlotWindow::filter([1796893200],'2026-12-11','UTC',60);throw new RuntimeException('missed');}catch(DomainException $e){}
echo 'PASS '.basename(__FILE__)."\n";
