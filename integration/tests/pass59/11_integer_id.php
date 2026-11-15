<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check(LifecycleLegacyId::parse(2147483647,'booking')===2147483647);
echo "PASS59 11_integer_id\n";
