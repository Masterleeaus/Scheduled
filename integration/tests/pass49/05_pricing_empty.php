<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check49(\Titan\BookingSidecar\BookingPricingRuleLimit::complete([])===[],'Empty set');
echo "PASS49 5 pricing_empty OK\n";
