<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\BookingTestExitPolicy;
$drivers=PDO::getAvailableDrivers();$mysql=in_array('mysql',$drivers,true);
$report=['php_version'=>PHP_VERSION,'pdo_mysql'=>$mysql,'mysql_test_dsn_configured'=>getenv('BOOKING_TEST_DSN')!==false,'runner_status'=>$mysql?'available':'skipped'];
echo json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
exit($mysql?0:77);
