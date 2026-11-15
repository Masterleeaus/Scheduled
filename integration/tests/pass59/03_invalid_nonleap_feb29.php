<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecyclePersistedTimestamp::parse('2027-02-29 09:00:00');});
echo "PASS59 03_invalid_nonleap_feb29\n";
