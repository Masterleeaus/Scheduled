<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new Pass49Database;\Titan\BookingSidecar\BookingTenantScopeGuard::assertConnection($db,['database'=>'tenant_a']);
echo "PASS49 21 tenant_valid OK\n";
