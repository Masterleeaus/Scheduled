<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('CC','2026-11-12 10:00:00','2',8)])->verifyCancelled(7,'CC'),'Wrong locked');
echo 'PASS52 08_cancel_wrong_id OK';
