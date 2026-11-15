<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicSlotFormat.php';
require_once __DIR__.'/../../core/app/Booking/Public/PublicSlotProjection.php';
use App\Booking\Public\{PublicSlotFormat,PublicSlotProjection};
try{PublicSlotProjection::fromSidecar(['slots'=>['2026-11-09T10:00:00+11:00','2026-11-09T10:00:00+11:00']],['timezone'=>'Australia/Melbourne','date'=>'2026-11-09']);throw new Exception('accepted');}catch(RuntimeException $e){}
echo "PASS slot-duplicate\n";
