<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
$rows=[['start'=>'2026-10-04 01:30:00','duration_minutes'=>120]];$v=B\BookingBusyWindow::fromRecords($rows,'Australia/Melbourne');verify($v[0][1]-$v[0][0]===7200,'DST elapsed duration');
echo 'PASS 19_busywindow_elapsed.php\n';
