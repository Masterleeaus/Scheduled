<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecycleLegacyId::parse(9.0,'booking');});
echo "PASS59 16_id_float\n";
