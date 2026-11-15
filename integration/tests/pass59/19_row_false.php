<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecycleReadbackRows::exactlyOne(false,'booking');});
echo "PASS59 19_row_false\n";
