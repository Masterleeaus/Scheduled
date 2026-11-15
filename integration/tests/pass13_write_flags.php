<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';$s=['booking_writes_enabled'=>true,'booking_schema_verified'=>true,'booking_rate_mapping_verified'=>true];if(\Titan\BookingSidecar\BookingWriteFeatureGate::enabled($s))throw new Exception('unverified concurrency enabled');$s['booking_concurrency_verified']=true;if(!\Titan\BookingSidecar\BookingWriteFeatureGate::enabled($s))throw new Exception('valid flags rejected');
echo "PASS ".basename(__FILE__)."\n";
