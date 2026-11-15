<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
rejected(fn()=>B\BookingConflictRowStaff::assertContains('1,2',3));
echo 'PASS 04_staff_membership.php\n';
