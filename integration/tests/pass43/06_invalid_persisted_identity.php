<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
rejected(fn()=>B\BookingConflictRowIdentity::parse(['id'=>'2x']));
echo 'PASS 06_invalid_persisted_identity.php\n';
