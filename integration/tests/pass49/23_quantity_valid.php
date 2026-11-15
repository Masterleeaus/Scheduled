<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$row=['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'method_unit_qty'=>'4.000'];$s=['service_id'=>1,'method_id'=>2,'method_unit_id'=>3,'quantity'=>4];\Titan\BookingSidecar\BookingWriteProofService::check($row,$s);
echo "PASS49 23 quantity_valid OK\n";
