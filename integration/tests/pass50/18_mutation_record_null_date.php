<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=row50();$r['booking_date_time']=null;fail50(fn()=>\Titan\BookingSidecar\BookingMutationRecord::parse($r),'Invalid legacy booking start');
echo "PASS50 18_mutation_record_null_date OK\n";
