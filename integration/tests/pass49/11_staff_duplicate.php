<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject49(fn()=>\Titan\BookingSidecar\BookingStrictStaffIds::parse('2,2'),'Duplicate');
echo "PASS49 11 staff_duplicate OK\n";
