<?php
declare(strict_types=1);
require_once __DIR__.'/../../core/app/Booking/Public/PublicSlotFormat.php';
use App\Booking\Public\{PublicSlotFormat};
assert(PublicSlotFormat::normalize('2026-11-09T10:00:00+11:00','Australia/Melbourne')==='2026-11-09T10:00:00+11:00');try{PublicSlotFormat::normalize('<script>','Australia/Melbourne');throw new Exception('accepted');}catch(RuntimeException $e){}
echo "PASS slot-format\n";
