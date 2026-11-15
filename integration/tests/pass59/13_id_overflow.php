<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecycleLegacyId::parse('2147483648','booking');});
echo "PASS59 13_id_overflow\n";
