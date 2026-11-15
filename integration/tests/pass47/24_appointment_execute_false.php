<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->ok=false;$p->queue=[$s];fails47(fn()=>(new \Titan\BookingSidecar\BookingLegacyAppointmentWriter($p))->insert(2,3,command47(),'49.00'),'Appointment insert failed');
echo 'PASS47 24 appointment_execute_false OK'.PHP_EOL;
