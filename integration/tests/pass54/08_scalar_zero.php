<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->stmt->column=0;check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::scalar($db,'SELECT 0'),false,'scalar zero accepted');
echo "PASS54 08 scalar_zero OK\n";
