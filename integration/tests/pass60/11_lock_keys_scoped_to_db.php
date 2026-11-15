<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/mysql/bootstrap.php';
require dirname(__DIR__,2).'/mysql/pass40/bin/validate_setup.php';
require dirname(__DIR__,2).'/mysql/pass40/bin/validate_fixture.php';
require dirname(__DIR__,2).'/mysql/pass40/bin/assert_lock_contention.php';
use Titan\BookingIntegration\MysqlTestConfig;
use Titan\BookingIntegration\DisposableName;
use Titan\BookingIntegration\LifecycleFixtureLoader;
function good():array{return ['BOOKING_TEST_DB_NAME'=>'titan_booking_test_acceptance','BOOKING_TEST_DB_HOST'=>'mysql-test','BOOKING_TEST_DB_USER'=>'booking_tester','BOOKING_TEST_DB_PASSWORD'=>'local_only','BOOKING_TEST_HOST'=>'mysql-test','BOOKING_TEST_DATABASE'=>'titan_booking_test_acceptance','BOOKING_TEST_USER'=>'booking_tester','BOOKING_TEST_PASSWORD'=>'local_only'];}
function ok(bool $v,string $why):void {if(!$v)throw new RuntimeException($why);}
function rejects(callable $f,string $why):void {try{$f();}catch(DomainException|InvalidArgumentException $e){return;}throw new RuntimeException($why);}
$a=Pass40Locks::plan('titan_booking_test_acceptance',1);$b=Pass40Locks::plan('titan_booking_test_acceptance',2);ok($a!==$b && strlen($a)<64,'keys should differ and fit MySQL');
echo "PASS60 11_lock_keys_scoped_to_db\n";
