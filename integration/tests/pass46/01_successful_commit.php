<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO; $v=(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>77); check46($v===77&&$p->begins===1&&$p->commits===1&&$p->rollbacks===0&&!$p->active,'success');
echo "pass46 01 successful_commit OK\n";
