<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::query($db,'SELECT 1'),false,'query(true)');
echo "PASS54 02 query_success OK\n";
