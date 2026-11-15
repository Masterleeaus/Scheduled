<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('RS')])->verifyRescheduled(7,'2026-11-12 11:00:00',[2]),'time mismatch');
echo 'PASS52 13_resched_wrong_time OK';
