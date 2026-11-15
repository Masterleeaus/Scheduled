<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->emulation=true;check54(fn()=>Titan\BookingIntegration\SessionSafetyProbe::assert($db),true,'emulation forbidden');
echo "PASS54 12 session_bad_emulation OK\n";
