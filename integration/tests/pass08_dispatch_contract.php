<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\Dispatcher;
// PHP PDO reflection bypasses constructor solely to verify dispatch of the DB-independent capability endpoint.
$db=(new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();
$out=(new Dispatcher($db))->dispatch('booking_preview_capabilities',[]);
if($out['writes_enabled']!==false||$out['supports_quote']!==true)throw new RuntimeException('Unsafe dispatch capability');
try{(new Dispatcher($db))->dispatch('create_booking',[]);throw new RuntimeException('Unexpected write route');}catch(InvalidArgumentException $e){}
echo 'PASS '.basename(__FILE__)."\n";
