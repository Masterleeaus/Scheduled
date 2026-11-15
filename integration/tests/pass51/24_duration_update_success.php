<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
(new \Titan\BookingSidecar\BookingRescheduleDurationWriter(db51()))->update(11,60);
echo 'PASS51 24_duration_update_success OK';
