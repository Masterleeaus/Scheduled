<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';$caps=\Titan\BookingSidecar\BookingOperationStatus::forTenant([]);if($caps['public_checkout']!==false||$caps['owner_create']!==false)throw new Exception('unsafe capability');
echo "PASS ".basename(__FILE__)."\n";
