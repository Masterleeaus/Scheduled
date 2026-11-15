<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecyclePersistedTimestamp::parse('2027-13-01 09:00:00');});
echo "PASS59 04_invalid_month13\n";
