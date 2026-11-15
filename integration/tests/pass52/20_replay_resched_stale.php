<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('RS','2026-11-12 11:00:00')])->verifyReplay(7,'reschedule',good52()),'time or staff mismatch');
echo 'PASS52 20_replay_resched_stale OK';
