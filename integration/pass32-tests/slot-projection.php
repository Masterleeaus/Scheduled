<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicSlotFormat.php';
require_once __DIR__.'/../../core/app/Booking/Public/PublicSlotProjection.php';
use App\Booking\Public\{PublicSlotFormat,PublicSlotProjection};
$p=['timezone'=>'Australia/Melbourne','date'=>'2026-11-09'];$r=PublicSlotProjection::fromSidecar(['slots'=>['2026-11-09T10:00:00+11:00']],$p);assert(count($r['available_starts'])===1);assert($r['booking_writes_enabled']===false);
echo "PASS slot-projection\n";
