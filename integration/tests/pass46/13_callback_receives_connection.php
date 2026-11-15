<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$v=(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn($db)=>$db===$p);check46($v===true,'connection changed');
echo "pass46 13 callback_receives_connection OK\n";
