<?php
declare(strict_types=1);
$s=file_get_contents(__DIR__.'/../mysql/lifecycle/LifecycleRunner.php');foreach(['LifecycleReadbackAudit::verify','LifecycleReplayOrderEvidence::verify','LifecycleCancellationStatus::verify'] as $x)if(!str_contains($s,$x))throw new RuntimeException('Not wired: '.$x);echo 'PASS pass26_runner_wired'.PHP_EOL;
