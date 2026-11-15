<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecycleLegacyId::parse(0,'booking');});
echo "PASS59 14_id_zero\n";
