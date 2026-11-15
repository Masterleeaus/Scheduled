<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/bootstrap.php';
require_once dirname(__DIR__,3).'/private/booking-sidecar/bootstrap.php';
spl_autoload_register(static function(string $class):void {
 $prefix='Titan\\BookingIntegration\\';
 if(!str_starts_with($class,$prefix))return;
 $file=__DIR__.'/'.substr($class,strlen($prefix)).'.php';
 if(is_file($file))require_once $file;
});
