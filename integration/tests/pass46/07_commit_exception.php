<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->commitThrows=true;reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>9),'commit exception');check46($p->rollbacks===1,'commit throw missing rollback');
echo "pass46 07 commit_exception OK\n";
