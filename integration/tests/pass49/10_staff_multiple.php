<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check49(\Titan\BookingSidecar\BookingStrictStaffIds::parse('1,2,345')===[1,2,345],'Multiple staff');
echo "PASS49 10 staff_multiple OK\n";
