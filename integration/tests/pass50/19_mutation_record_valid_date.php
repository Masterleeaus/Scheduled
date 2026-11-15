<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=\Titan\BookingSidecar\BookingMutationRecord::parse(row50());ok50($r['start']==='2026-11-12 10:00:00','Valid timestamp');
echo "PASS50 19_mutation_record_valid_date OK\n";
