<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php'; $future=gmdate('Y-m-d H:i:s',time()+3600);$command=['window'=>['start'=>time()+3600]];\Titan\BookingSidecar\BookingCreationPolicy::assertScope([], $command);try{\Titan\BookingSidecar\BookingCreationPolicy::assertScope(['addons'=>[1]],$command);throw new Exception('failed');}catch(DomainException $e){}
echo "PASS ".basename(__FILE__)."\n";
