<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52()],true,false)->verifyCancelled(7,'CC'),'query failed');
echo 'PASS52 09_cancel_db_failed OK';
