<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject49(fn()=>\Titan\BookingSidecar\BookingStrictStaffIds::parse('01'),'Invalid stored staff');
echo "PASS49 14 staff_leading_zero OK\n";
