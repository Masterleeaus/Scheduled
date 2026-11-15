<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->stmt->all=[];check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::rows($db,'SELECT * FROM existing'),false,'empty result accepted');
echo "PASS54 11 rows_empty_success OK\n";
