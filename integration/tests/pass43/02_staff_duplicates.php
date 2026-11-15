<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
rejected(fn()=>B\BookingConflictRowStaff::members('2,2'));
echo 'PASS 02_staff_duplicates.php\n';
