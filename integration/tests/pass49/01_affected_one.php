<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$s=new Pass49Statement;$s->rows=1;\Titan\BookingSidecar\BookingAffectedRows::inserted($s,'Appointment');
echo "PASS49 1 affected_one OK\n";
