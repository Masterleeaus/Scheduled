<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->prepareFail=true;check54(fn()=>\Titan\BookingIntegration\CheckedProbeSql::execute($db,'SELECT ?', [1]),true,'prepare(false)');
echo "PASS54 03 prepare_false OK\n";
