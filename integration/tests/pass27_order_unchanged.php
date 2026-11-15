<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$a=['order_id'=>20,'order_duration'=>120];\Titan\BookingIntegration\LifecycleOrderIntegrity::verify($a,$a);
echo "PASS pass27_order_unchanged\n";
