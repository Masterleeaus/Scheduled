<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject49(fn()=>\Titan\BookingSidecar\BookingStrictStaffIds::parse('0'),'Invalid stored staff');
echo "PASS49 13 staff_zero OK\n";
