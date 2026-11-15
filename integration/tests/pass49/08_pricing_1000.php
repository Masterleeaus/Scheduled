<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject49(fn()=>\Titan\BookingSidecar\BookingPricingRuleLimit::complete(array_fill(0,1000,1)),'truncated');
echo "PASS49 8 pricing_1000 OK\n";
