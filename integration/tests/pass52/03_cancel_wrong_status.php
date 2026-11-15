<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('A')])->verifyCancelled(7,'CC'),'status mismatch');
echo 'PASS52 03_cancel_wrong_status OK';
