<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$p->queue=[$s];p47((new \Titan\BookingSidecar\BookingLegacyClientWriter($p))->insert(2,customer47(),60)===27,'client id mismatch');p47(count($s->input)===6,'customer insert args');
echo 'PASS47 22 customer_success OK'.PHP_EOL;
