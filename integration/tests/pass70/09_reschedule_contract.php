<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$s=file_get_contents(dirname(__DIR__,3).'/private/booking-sidecar/BookingRescheduleCoordinator.php');p70(substr_count($s,',$lockDeadlineNs)')===2,'rescheduling phases not using shared deadline');p70(str_contains($s,'$lockDeadlineNs=hrtime(true)+5000000000'),'deadline not captured before operations');
echo 'PASS70 09_reschedule_contract.php OK'.PHP_EOL;
