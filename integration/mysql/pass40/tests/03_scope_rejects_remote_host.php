<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/validate_setup.php';
if(Pass40Setup::check(['BOOKING_TEST_DB_NAME'=>'titan_booking_test_acceptance','BOOKING_TEST_DB_HOST'=>'production.example','BOOKING_TEST_DB_USER'=>'tester','BOOKING_TEST_DB_PASSWORD'=>'secret'],['mysql'])['ready'])throw new Exception('must fail');
echo 'PASS 03_scope_rejects_remote_host'.PHP_EOL;
