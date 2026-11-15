<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('C')])->verifyRescheduled(7,'2026-11-12 10:00:00',[2]),'status mismatch');
echo 'PASS52 12_resched_wrong_status OK';
