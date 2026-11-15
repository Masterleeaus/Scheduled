<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->commitOk=false;reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>9),'commit failed');check46($p->rollbacks===1,'failed commit missing rollback');
echo "pass46 06 commit_false OK\n";
