<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{MysqlTestConfig,DisposableName,MysqlDriverCheck,SchemaInventory,TransactionalTableAudit,AcceptanceReport,ConcurrentAttemptPolicy};
function ok(bool $ok):void{if(!$ok)throw new RuntimeException('Assertion failed');}
function deny(callable $f):void{try{$f();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$r=AcceptanceReport::build([],[],true,true);ok($r['accepted']===true&&$r['booking_creation_verified']===false&&$r['concurrency_verified']===false);
echo 'PASS pass18_report_readonly'.PHP_EOL;
