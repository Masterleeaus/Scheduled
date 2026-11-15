<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecyclePersistedTimestamp::parse('2100-01-01 09:00:00');});
echo "PASS59 09_invalid_year_range\n";
