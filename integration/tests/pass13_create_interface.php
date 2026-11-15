<?php
declare(strict_types=1);
$s=file_get_contents(__DIR__.'/../../core/resources/views/user/booking-management.blade.php');
foreach(['titan-booking-create','method_unit_id','staff_ids','start_at','Customer email'] as $part)if(!str_contains($s,$part))throw new Exception('Missing form input '.$part);
echo "PASS booking form\n";
