<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecycleReadbackRows::exactlyOne([[]],'booking');});
echo "PASS59 20_row_empty\n";
