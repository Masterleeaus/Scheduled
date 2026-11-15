<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/mysql/bootstrap.php';
require dirname(__DIR__,2).'/mysql/pass40/bin/validate_setup.php';
require dirname(__DIR__,2).'/mysql/pass40/bin/validate_fixture.php';
require dirname(__DIR__,2).'/mysql/pass40/bin/assert_lock_contention.php';
use Titan\BookingIntegration\MysqlTestConfig;
use Titan\BookingIntegration\DisposableName;
use Titan\BookingIntegration\LifecycleFixtureLoader;
require_once dirname(__DIR__,2).'/mysql/lifecycle/LifecycleFixtureLoader.php';
function good():array{return ['BOOKING_TEST_DB_NAME'=>'titan_booking_test_acceptance','BOOKING_TEST_DB_HOST'=>'mysql-test','BOOKING_TEST_DB_USER'=>'booking_tester','BOOKING_TEST_DB_PASSWORD'=>'local_only','BOOKING_TEST_HOST'=>'mysql-test','BOOKING_TEST_DATABASE'=>'titan_booking_test_acceptance','BOOKING_TEST_USER'=>'booking_tester','BOOKING_TEST_PASSWORD'=>'local_only'];}
function ok(bool $v,string $why):void {if(!$v)throw new RuntimeException($why);}
function rejects(callable $f,string $why):void {try{$f();}catch(DomainException|InvalidArgumentException $e){return;}throw new RuntimeException($why);}
$f=tempnam(sys_get_temp_dir(),'p60');$link=$f.'.link';file_put_contents($f,'{}');symlink($f,$link);try{rejects(fn()=>LifecycleFixtureLoader::read($link),'symlink fixture');}finally{unlink($link);unlink($f);}
echo "PASS60 16_fixture_rejects_symlink\n";
