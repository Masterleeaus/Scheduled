<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingPreviewRequest,BookingPreviewModifiers,BookingPreviewSettings,BookingPreviewPrice,BookingPreviewSlot,BookingPreviewIntersection,BookingPreviewSlotWindow,BookingPreviewDisplaySlots,BookingPreviewStaffPolicy,BookingPreviewSchedule,BookingPreviewResult,BookingPreviewPlanner,BookingPreviewRateAssumptions,BookingPreviewLimit,BookingPreviewAvailabilityQuery,BookingPreviewReadiness,BookingPreviewReader};
$s=array_fill_keys(['tax_enabled','discount_enabled','coupon_enabled','addons_enabled','travel_fee_enabled','deposit_enabled'],false);BookingPreviewSettings::assertVerified($s);unset($s['tax_enabled']);try{BookingPreviewSettings::assertVerified($s);throw new RuntimeException('missed');}catch(DomainException $e){}
echo 'PASS '.basename(__FILE__)."\n";
