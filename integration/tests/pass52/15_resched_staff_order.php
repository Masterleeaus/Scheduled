<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
proof52([row52('RS','2026-11-12 10:00:00','2,3')])->verifyRescheduled(7,'2026-11-12 10:00:00',[3,2]);
echo 'PASS52 15_resched_staff_order OK';
