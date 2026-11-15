<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$s=new Pass49Statement;$s->rows=0;reject49(fn()=>\Titan\BookingSidecar\BookingAffectedRows::inserted($s,'Appointment'),'did not insert exactly one');
echo "PASS49 2 affected_zero OK\n";
