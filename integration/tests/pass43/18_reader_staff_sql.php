<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
$q=B\BookingConflictReadbackSql::query();verify(str_contains($q,'FIND_IN_SET')&&str_contains($q,'booking_status'),'staff filter');
echo 'PASS 18_reader_staff_sql.php\n';
