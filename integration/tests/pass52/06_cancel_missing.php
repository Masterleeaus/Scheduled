<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([])->verifyCancelled(7,'CC'),'not found');
echo 'PASS52 06_cancel_missing OK';
