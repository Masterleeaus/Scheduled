<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/scan_archive.php';
$r=Pass40Archive::check(['private/cleanto/book.php','core/app/Booking/Public/Redemption/CommitOrchestrator.php','integration/mysql/lifecycle/run_lifecycle.php']);if(!$r['complete'])throw new Exception('expected complete');
echo 'PASS 19_archive_accepts_required_source'.PHP_EOL;
