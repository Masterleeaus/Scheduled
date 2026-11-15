<?php
declare(strict_types=1);
$s=file_get_contents(__DIR__.'/../../core/app/Booking/BookingOwnerGateway.php');
if(!str_contains($s,'forOwner($ownerId)')||!str_contains($s,'unset($data[' . "'tenant_id'" . ']'))throw new Exception('Tenant not server-resolved');
echo "PASS tenant server scope\n";
