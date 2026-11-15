<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
B\BookingConflictWindowSafety::assert(['start'=>1,'end'=>3601]);
echo 'PASS 16_window_valid.php\n';
