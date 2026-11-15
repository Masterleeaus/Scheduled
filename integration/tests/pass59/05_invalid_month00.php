<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecyclePersistedTimestamp::parse('2027-00-01 09:00:00');});
echo "PASS59 05_invalid_month00\n";
