<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicAvailabilityResponse.php';
use App\Booking\Public\{PublicAvailabilityResponse};
$r=PublicAvailabilityResponse::guard(['available_starts'=>[],'preview_only'=>true,'booking_writes_enabled'=>false,'appointment_created'=>false]);assert($r['appointment_created']===false);try{PublicAvailabilityResponse::guard(['available_starts'=>[],'preview_only'=>true,'booking_writes_enabled'=>true,'appointment_created'=>false]);throw new Exception('accepted');}catch(RuntimeException $e){}
echo "PASS response\n";
