<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->stmt->column=false;check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::scalar($db,'SELECT 1'),true,'scalar false');
echo "PASS54 06 scalar_false OK\n";
