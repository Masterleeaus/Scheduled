<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$q=new Pass47Statement;$q->data=[['id'=>27,'client_id'=>1,'order_id'=>2]];$o=new Pass47Statement;$o->ok=false;$p->queue=[$q,$o];fails47(fn()=>\Titan\BookingSidecar\BookingWriteProofReader::read($p,27),'Customer order proof query failed');
echo 'PASS47 29 proof_customer_execute_false OK'.PHP_EOL;
