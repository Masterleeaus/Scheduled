<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->stmt->column=null;check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::scalar($db,'SELECT 1'),true,'scalar null');
echo "PASS54 07 scalar_null OK\n";
