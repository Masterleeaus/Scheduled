<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=row51();$r['booking_date_time']='2026-02-30 10:00:00';rejects51(fn()=>\Titan\BookingSidecar\BookingMutationRecord::parse($r),'calendar date');
echo 'PASS51 21_duration_invalid_date OK';
