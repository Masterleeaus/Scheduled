<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(function()use($p){$p->active=false;return 10;}),'closed transaction');check46($p->commits===0,'closed tx commit');
echo "pass46 10 callback_closes_transaction OK\n";
