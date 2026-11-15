<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$p->queue=[$s];p47((new \Titan\BookingSidecar\BookingLegacyAppointmentWriter($p))->insert(2,3,command47(),'49.00')===27,'booking id mismatch');p47(count($s->input)===10,'booking args incorrect');
echo 'PASS47 25 appointment_success OK'.PHP_EOL;
