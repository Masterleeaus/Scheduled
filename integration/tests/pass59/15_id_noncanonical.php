<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
expectFailure(static function():void {LifecycleLegacyId::parse('0009','booking');});
echo "PASS59 15_id_noncanonical\n";
