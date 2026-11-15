<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new Pass49Database;$db->failQuery=true;reject49(fn()=>\Titan\BookingSidecar\BookingDatabaseLookup::current($db),'query failed');
echo "PASS49 17 database_query_false OK\n";
