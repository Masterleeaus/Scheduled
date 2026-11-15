<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('CC')])->verifyCancelled(7,'C'),'status mismatch');
echo 'PASS52 05_cancel_wrong_target OK';
