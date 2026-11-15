<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject53(static function():void { $db=db53();$db->active=false;prove53(null,null,null,$db); }, 'transaction');
echo 'PASS53 23_no_transaction OK';
