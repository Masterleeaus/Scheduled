<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject49(fn()=>\Titan\BookingSidecar\BookingStrictStaffIds::parse('1,,2'),'Invalid stored staff');
echo "PASS49 15 staff_separator OK\n";
