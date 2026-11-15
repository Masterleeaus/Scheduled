<?php
declare(strict_types=1);
require __DIR__.'/../pass42/bootstrap.php';
use Titan\BookingSidecar as B;
$root=dirname(__DIR__,3);verify(is_dir($root.'/private/cleanto'),'Cleanto original import');
echo 'PASS 21_original_writer_preserved.php\n';
