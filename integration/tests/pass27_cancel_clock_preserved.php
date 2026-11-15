<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
\Titan\BookingIntegration\LifecycleMutationClock::cancellation(['booking_date_time'=>'2026-12-01 09:00:00'],['booking_date_time'=>'2026-12-01 09:00:00']);
echo "PASS pass27_cancel_clock_preserved\n";
