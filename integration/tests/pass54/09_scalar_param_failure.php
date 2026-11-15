<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->stmt->executed=false;check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::scalar($db,'SELECT ?', [9]),true,'parameterized scalar failure');
echo "PASS54 09 scalar_param_failure OK\n";
