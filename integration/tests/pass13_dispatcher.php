<?php
declare(strict_types=1);
$s=file_get_contents(__DIR__.'/../../private/booking-sidecar/RequestHandler.php');foreach(['owner_booking_list','owner_booking_create','owner_booking_cancel','owner_booking_reschedule'] as $name)if(!str_contains($s,$name))throw new Exception('Missing sidecar command');
echo "PASS ".basename(__FILE__)."\n";
