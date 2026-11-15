<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('RS')])->verifyReplay(7,'reschedule'),'command missing');
echo 'PASS52 21_replay_missing_command OK';
