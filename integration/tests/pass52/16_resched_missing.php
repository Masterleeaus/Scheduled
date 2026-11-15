<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([])->verifyRescheduled(7,'2026-11-12 10:00:00',[2]),'not found');
echo 'PASS52 16_resched_missing OK';
