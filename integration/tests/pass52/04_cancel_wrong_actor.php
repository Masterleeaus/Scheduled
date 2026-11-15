<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('CS')])->verifyCancelled(7,'CC'),'status mismatch');
echo 'PASS52 04_cancel_wrong_actor OK';
