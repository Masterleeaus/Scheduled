<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/assert_lock_contention.php';
try{Pass40Locks::plan('production',1);throw new Exception('must fail');}catch(InvalidArgumentException $e){}
echo 'PASS 13_locks_reject_production'.PHP_EOL;
