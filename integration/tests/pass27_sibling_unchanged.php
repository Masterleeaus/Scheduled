<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$a=[2=>['id'=>2,'booking_status'=>'A']];\Titan\BookingIntegration\LifecycleSiblingIntegrity::verify($a,$a);
echo "PASS pass27_sibling_unchanged\n";
