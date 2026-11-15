<?php
declare(strict_types=1);
$s=file_get_contents(__DIR__.'/../../private/booking-sidecar/BookingRescheduleCoordinator.php');
foreach(['BookingMutationLedger','BookingMutationReplay','->claim(','->complete('] as $part){if(!str_contains($s,$part))throw new Exception('Missing idempotency guard '.$part);}
echo "PASS reschedule ledger\n";
