<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/validate_setup.php';
if(Pass40Setup::check(['BOOKING_TEST_DB_NAME'=>'production','BOOKING_TEST_DB_HOST'=>'mysql-test','BOOKING_TEST_DB_USER'=>'tester','BOOKING_TEST_DB_PASSWORD'=>'secret'],['mysql'])['ready'])throw new Exception('must fail');
echo 'PASS 02_scope_rejects_other_db'.PHP_EOL;
