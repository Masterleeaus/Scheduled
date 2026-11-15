<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
$pdo=(new ReflectionClass(\PDO::class))->newInstanceWithoutConstructor();
$d=new \Titan\BookingSidecar\Dispatcher($pdo);
foreach(['booking_create','booking_cancel','booking_reschedule'] as $operation){
 try{$d->dispatch($operation,[]);throw new RuntimeException('Write exposed: '.$operation);}catch(InvalidArgumentException $e){}
}
echo "pass05_write_gate OK\n";
