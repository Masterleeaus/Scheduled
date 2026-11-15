<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>(new \Titan\BookingSidecar\BookingMutationReader(reader50([row50()],false)))->locked(27),'query failed');
echo "PASS50 13_mutation_reader_query_failure OK\n";
