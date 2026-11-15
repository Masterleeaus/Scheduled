<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('CO')])->verifyReplay(7,'cancel'),'no longer matches');
echo 'PASS52 30_replay_cancel_completed OK';
