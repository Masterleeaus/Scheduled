<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject53(static function():void { $db=db53();$db->failPrepare=true;prove53(null,null,null,$db); }, 'Booking proof query failed');
echo 'PASS53 24_failed_prepare OK';
