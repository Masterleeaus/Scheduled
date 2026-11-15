<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('CC'),row52('CC')])->verifyCancelled(7,'CC'),'Duplicate');
echo 'PASS52 07_cancel_duplicate OK';
