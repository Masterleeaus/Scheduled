<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check(LifecyclePersistedTimestamp::parse('2028-02-29 09:00:00')==='2028-02-29 09:00:00');
echo "PASS59 01_valid_leap_day\n";
