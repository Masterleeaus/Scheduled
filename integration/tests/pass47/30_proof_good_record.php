<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$q=new Pass47Statement;$q->data=[['id'=>27,'client_id'=>1,'order_id'=>2]];$o=new Pass47Statement;$o->data=[['id'=>1,'order_id'=>2]];$p->queue=[$q,$o];$rows=\Titan\BookingSidecar\BookingWriteProofReader::read($p,27);p47($rows[0]['id']===27&&$rows[1]['id']===1,'proof rows');
echo 'PASS47 30 proof_good_record OK'.PHP_EOL;
