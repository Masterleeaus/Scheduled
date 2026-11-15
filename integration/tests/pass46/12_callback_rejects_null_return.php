<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$v=(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>null);check46($v===null&&$p->commits===1,'null callback value wrong');
echo "pass46 12 callback_rejects_null_return OK\n";
