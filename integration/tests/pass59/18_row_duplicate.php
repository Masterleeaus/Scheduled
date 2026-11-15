<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecycleReadbackRows::exactlyOne([['id'=>9],['id'=>9]],'booking');});
echo "PASS59 18_row_duplicate\n";
