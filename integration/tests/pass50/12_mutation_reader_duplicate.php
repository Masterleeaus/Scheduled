<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>(new \Titan\BookingSidecar\BookingMutationReader(reader50([row50(),row50()])))->locked(27),'Duplicate');
echo "PASS50 12_mutation_reader_duplicate OK\n";
