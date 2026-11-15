<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/acceptance_report.php';
$r=Pass40Report::build([]);if($r['release_ready'])throw new Exception('premature readiness');
echo 'PASS 17_report_never_assumes_payment'.PHP_EOL;
