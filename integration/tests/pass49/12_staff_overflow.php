<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject49(fn()=>\Titan\BookingSidecar\BookingStrictStaffIds::parse('99999999999999999999999999999'),'overflow');
echo "PASS49 12 staff_overflow OK\n";
