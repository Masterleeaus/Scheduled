<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
\Titan\BookingIntegration\LifecycleMutationStatus::rescheduled(['booking_status'=>'A'],['booking_status'=>'RS']);
echo "PASS pass27_reschedule_status_valid\n";
