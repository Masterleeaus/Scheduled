<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecyclePersistedTimestamp::parse('2027-01-01 24:00:00');});
echo "PASS59 06_invalid_hour24\n";
