<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{MysqlTestConfig,DisposableName,MysqlDriverCheck,SchemaInventory,TransactionalTableAudit,AcceptanceReport,ConcurrentAttemptPolicy};
function ok(bool $ok):void{if(!$ok)throw new RuntimeException('Assertion failed');}
function deny(callable $f):void{try{$f();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$a=SchemaInventory::required();unset($a['ct_bookings']);ok(in_array('ct_bookings.id',SchemaInventory::missing($a),true));
echo 'PASS pass18_schema_missing'.PHP_EOL;
