<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
rejected(fn()=>B\BookingConflictRowTime::instant('2026-10-04 02:30:00','Australia/Melbourne'));
echo 'PASS 11_dst_nonexistent_legacy_row.php\n';
