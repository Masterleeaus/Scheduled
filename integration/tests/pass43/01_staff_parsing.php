<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
verify(B\BookingConflictRowStaff::members('2, 7,15')===[2,7,15],'legacy staff CSV');
echo 'PASS 01_staff_parsing.php\n';
