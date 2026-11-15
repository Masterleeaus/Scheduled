<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/assert_lock_contention.php';
$a=Pass40Locks::plan('titan_booking_test_acceptance',2);$b=Pass40Locks::plan('titan_booking_test_acceptance',2);if($a!==$b)throw new Exception('not deterministic');
echo 'PASS 11_locks_are_deterministic'.PHP_EOL;
