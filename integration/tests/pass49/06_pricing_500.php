<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=array_fill(0,500,['id'=>1]);check49(count(\Titan\BookingSidecar\BookingPricingRuleLimit::complete($r))===500,'500 rows');
echo "PASS49 6 pricing_500 OK\n";
