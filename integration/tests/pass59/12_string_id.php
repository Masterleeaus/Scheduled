<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check(LifecycleLegacyId::parse('9','booking')===9);
echo "PASS59 12_string_id\n";
