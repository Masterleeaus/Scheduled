<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
rejected(fn()=>B\BookingConflictWindowSafety::assert(['start'=>5,'end'=>5]));
echo 'PASS 15_window_bounds.php\n';
