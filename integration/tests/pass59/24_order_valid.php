<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check(LifecycleClientOrderRow::parse(p59_order())['order_id']===77);
echo "PASS59 24_order_valid\n";
