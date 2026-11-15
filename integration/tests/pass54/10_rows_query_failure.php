<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->queryFail=true;check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::rows($db,'SELECT * FROM missing'),true,'rows query failure');
echo "PASS54 10 rows_query_failure OK\n";
