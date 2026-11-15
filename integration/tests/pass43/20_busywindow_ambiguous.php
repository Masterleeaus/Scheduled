<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
rejected(fn()=>B\BookingBusyWindow::fromRecords([['start'=>'2026-04-05 02:30:00','duration_minutes'=>60]],'Australia/Melbourne'));
echo 'PASS 20_busywindow_ambiguous.php\n';
