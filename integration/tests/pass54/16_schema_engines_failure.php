<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->queryFail=true;check54(fn()=>Titan\BookingIntegration\TransactionalTableAudit::read($db),true,'storage metadata failure');
echo "PASS54 16 schema_engines_failure OK\n";
