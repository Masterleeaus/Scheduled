<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check49(\Titan\BookingSidecar\BookingStrictStaffIds::parse('7')===[7],'Single staff');
echo "PASS49 9 staff_single OK\n";
