<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
\Titan\BookingIntegration\LifecycleMutationStatus::cancelled(['booking_status'=>'RS'],['booking_status'=>'CC']);
echo "PASS pass27_cancel_status_valid\n";
