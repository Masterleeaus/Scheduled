<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->failPrepare=true;fails47(fn()=>(new \Titan\BookingSidecar\BookingLegacyAppointmentWriter($p))->insert(2,3,command47(),'49.00'),'Appointment insert failed');
echo 'PASS47 23 appointment_prepare_false OK'.PHP_EOL;
