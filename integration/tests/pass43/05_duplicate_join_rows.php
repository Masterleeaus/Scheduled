<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
rejected(fn()=>B\BookingConflictRowIdentity::unique([['id'=>2],['id'=>'2']]));
echo 'PASS 05_duplicate_join_rows.php\n';
