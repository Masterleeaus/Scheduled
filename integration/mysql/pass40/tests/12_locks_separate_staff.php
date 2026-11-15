<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/assert_lock_contention.php';
if(Pass40Locks::plan('titan_booking_test_acceptance',2)===Pass40Locks::plan('titan_booking_test_acceptance',3))throw new Exception('collision');
echo 'PASS 12_locks_separate_staff'.PHP_EOL;
