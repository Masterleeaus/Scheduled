<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::execute($db,'SELECT ?', [1]),false,'execute(true)');
echo "PASS54 05 execute_success OK\n";
