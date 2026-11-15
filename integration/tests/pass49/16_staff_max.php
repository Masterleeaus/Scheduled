<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$max=(string)PHP_INT_MAX;check49(\Titan\BookingSidecar\BookingStrictStaffIds::parse($max)===[PHP_INT_MAX],'Max staff');
echo "PASS49 16 staff_max OK\n";
