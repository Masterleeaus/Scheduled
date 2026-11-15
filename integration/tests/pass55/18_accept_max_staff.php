<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
a55(\Titan\BookingSidecar\BookingLockKeys::forStaff([2147483647])===['tb:staff:2147483647'],'max staff id rejected');
echo 'pass55 18_accept_max_staff OK\n';
