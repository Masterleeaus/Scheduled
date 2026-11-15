<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$s=file_get_contents(dirname(__DIR__,3).'/private/booking-sidecar/BookingCancellationCoordinator.php');p70(str_contains($s,'finally{$locks->releaseAll();}'),'cancellation lock release contract missing');
echo 'PASS70 10_cancellation_unchanged.php OK'.PHP_EOL;
