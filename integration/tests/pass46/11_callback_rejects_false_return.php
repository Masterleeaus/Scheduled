<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$v=(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>false);check46($v===false&&$p->commits===1,'callback value wrong');
echo "pass46 11 callback_rejects_false_return OK\n";
