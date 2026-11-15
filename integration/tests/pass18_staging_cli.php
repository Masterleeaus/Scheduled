<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
$s=file_get_contents(__DIR__.'/../mysql/run_acceptance.php');
$p=file_get_contents(__DIR__.'/../mysql/ConnectionPair.php');
if(!str_contains($s,"PHP_SAPI!=='cli'") || !str_contains($s,'ConnectionPair::create') || !str_contains($p,'IsolationSentinel::assert')){
 throw new RuntimeException('CLI and per-connection isolation enforcement must remain enabled');
}
echo 'PASS pass18_staging_cli'.PHP_EOL;
