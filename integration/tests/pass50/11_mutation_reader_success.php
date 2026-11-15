<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=(new \Titan\BookingSidecar\BookingMutationReader(reader50([row50()])))->locked(27);ok50($r['id']===27&&$r['status']==='A','Mutation normalized row');
echo "PASS50 11_mutation_reader_success OK\n";
