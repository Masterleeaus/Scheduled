<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
\Titan\BookingIntegration\LifecycleMutationClock::reschedule(['booking_date_time'=>'2026-12-02 09:00:00'],['start_at'=>'2026-12-02 09:00:00']);
echo "PASS pass27_move_time_matches\n";
