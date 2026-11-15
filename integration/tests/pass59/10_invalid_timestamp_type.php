<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecyclePersistedTimestamp::parse(20270101);});
echo "PASS59 10_invalid_timestamp_type\n";
