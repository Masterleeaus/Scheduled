<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check(LifecycleFullBookingRow::parse(p59_booking())['id']===9);
echo "PASS59 21_full_booking_valid\n";
