<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('CC')])->verifyReplay(7,'delete'),'Unknown mutation');
echo 'PASS52 22_replay_unknown_kind OK';
