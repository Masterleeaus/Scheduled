<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new Pass49Database;reject49(fn()=>\Titan\BookingSidecar\BookingTenantScopeGuard::assertConnection($db,['database'=>'tenant_b']),'differs');
echo "PASS49 22 tenant_wrong OK\n";
