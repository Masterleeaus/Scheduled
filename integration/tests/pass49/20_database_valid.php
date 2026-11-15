<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new Pass49Database;check49(\Titan\BookingSidecar\BookingDatabaseLookup::current($db)==='tenant_a','Wrong database');
echo "PASS49 20 database_valid OK\n";
