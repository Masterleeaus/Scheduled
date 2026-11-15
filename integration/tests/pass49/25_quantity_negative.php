<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$row=['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'method_unit_qty'=>'-1'];$s=['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'quantity'=>1];reject49(fn()=>\Titan\BookingSidecar\BookingWriteProofService::check($row,$s),'Quantity mismatch');
echo "PASS49 25 quantity_negative OK\n";
