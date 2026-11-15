<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
$q=B\BookingConflictReadbackSql::query();verify(str_contains($q,'LIMIT 5001')&&str_contains($q,'ct_order_client_info'),'bounded original join');
echo 'PASS 17_reader_query_bound.php\n';
