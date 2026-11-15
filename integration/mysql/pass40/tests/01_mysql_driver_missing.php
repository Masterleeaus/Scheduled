<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/validate_setup.php';
if(Pass40Setup::check(['BOOKING_TEST_DB_NAME'=>'titan_booking_test_acceptance','BOOKING_TEST_DB_HOST'=>'mysql-test','BOOKING_TEST_DB_USER'=>'tester','BOOKING_TEST_DB_PASSWORD'=>'secret'],[])['ready'])throw new Exception('must fail');
echo 'PASS 01_mysql_driver_missing'.PHP_EOL;
