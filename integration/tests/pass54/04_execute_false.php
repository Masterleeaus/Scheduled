<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->stmt->executed=false;check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::execute($db,'SELECT ?', [1]),true,'execute(false)');
echo "PASS54 04 execute_false OK\n";
