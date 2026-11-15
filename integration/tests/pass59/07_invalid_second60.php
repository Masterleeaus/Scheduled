<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecyclePersistedTimestamp::parse('2027-01-01 09:00:60');});
echo "PASS59 07_invalid_second60\n";
