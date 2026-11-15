<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
$q=strtoupper(B\BookingConflictReadbackSql::query());verify(str_starts_with($q,'SELECT ')&&!str_contains($q,'UPDATE '),'read only');
echo 'PASS 22_reader_no_write_sql.php\n';
