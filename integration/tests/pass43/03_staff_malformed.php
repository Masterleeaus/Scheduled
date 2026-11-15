<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
rejected(fn()=>B\BookingConflictRowStaff::members('2,x'));
echo 'PASS 03_staff_malformed.php\n';
