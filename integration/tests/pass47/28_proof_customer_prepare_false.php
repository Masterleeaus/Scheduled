<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$q=new Pass47Statement;$q->data=[['id'=>27,'client_id'=>1,'order_id'=>2]];$p->queue=[$q];$p->failPrepare=false; $p=new class extends Pass47PDO {private int $count=0;public function prepare(string $query,array $options=[]):PDOStatement|false{$this->count++;return $this->count===1?new class extends Pass47Statement{public function __construct(){$this->data=[['id'=>27,'client_id'=>1,'order_id'=>2]];}}:false;}};fails47(fn()=>\Titan\BookingSidecar\BookingWriteProofReader::read($p,27),'Customer order proof query failed');
echo 'PASS47 28 proof_customer_prepare_false OK'.PHP_EOL;
