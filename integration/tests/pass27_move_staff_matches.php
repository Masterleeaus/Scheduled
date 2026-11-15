<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
\Titan\BookingIntegration\LifecycleMutationStaff::reschedule(['staff_ids'=>[4,5]],['staff_ids'=>[4,5]]);
echo "PASS pass27_move_staff_matches\n";
