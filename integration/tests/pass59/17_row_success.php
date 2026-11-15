<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
check(LifecycleReadbackRows::exactlyOne([['id'=>9]],'booking')['id']===9);
echo "PASS59 17_row_success\n";
