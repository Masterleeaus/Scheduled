<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new Pass49Database;$db->database='';reject49(fn()=>\Titan\BookingSidecar\BookingDatabaseLookup::current($db),'not selected');
echo "PASS49 18 database_empty OK\n";
