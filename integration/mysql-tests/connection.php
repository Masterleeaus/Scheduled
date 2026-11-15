<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
function pass16_test_db(): \PDO {
 if(!extension_loaded('pdo_mysql'))throw new RuntimeException('pdo_mysql unavailable');
 $dsn=getenv('BOOKING_TEST_DSN')?:''; $user=getenv('BOOKING_TEST_USER')?:'';$pass=getenv('BOOKING_TEST_PASSWORD')?:'';
 if(!preg_match('/^mysql:.*(?:^|;)dbname=(titan_booking_test_[a-zA-Z0-9_]+)(?:;|$)/D',$dsn,$matches))throw new RuntimeException('Disposable titan_booking_test_* database required');
 $pdo=new PDO($dsn,$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false]);
 $name=$pdo->query('SELECT DATABASE()')->fetchColumn();
 if(!is_string($name)||!preg_match('/^titan_booking_test_[a-zA-Z0-9_]+$/D',$name))throw new RuntimeException('Unexpected connected database');
 return $pdo;
}
