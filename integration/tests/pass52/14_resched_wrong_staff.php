<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>proof52([row52('RS')])->verifyRescheduled(7,'2026-11-12 10:00:00',[3]),'staff mismatch');
echo 'PASS52 14_resched_wrong_staff OK';
