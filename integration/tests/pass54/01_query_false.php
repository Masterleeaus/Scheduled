<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->queryFail=true;check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::query($db,'SELECT 1'),true,'query(false)');
echo "PASS54 01 query_false OK\n";
