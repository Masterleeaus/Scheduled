<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject49(fn()=>\Titan\BookingSidecar\BookingPricingRuleLimit::complete(array_fill(0,501,1)),'truncated');
echo "PASS49 7 pricing_501 OK\n";
