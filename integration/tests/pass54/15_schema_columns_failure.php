<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->queryFail=true;check54(fn()=>Titan\BookingIntegration\SchemaInventoryReader::read($db),true,'schema metadata failure');
echo "PASS54 15 schema_columns_failure OK\n";
