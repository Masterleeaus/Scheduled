<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$s=new Pass49Statement;$s->rows=-1;reject49(fn()=>\Titan\BookingSidecar\BookingAffectedRows::inserted($s,'Customer order'),'did not insert exactly one');
echo "PASS49 4 affected_negative OK\n";
