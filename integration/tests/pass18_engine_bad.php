<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{MysqlTestConfig,DisposableName,MysqlDriverCheck,SchemaInventory,TransactionalTableAudit,AcceptanceReport,ConcurrentAttemptPolicy};
function ok(bool $ok):void{if(!$ok)throw new RuntimeException('Assertion failed');}
function deny(callable $f):void{try{$f();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$a=array_fill_keys(array_keys(SchemaInventory::required()),'InnoDB');$a['ct_bookings']='MyISAM';ok(TransactionalTableAudit::bad($a)===['ct_bookings']);
echo 'PASS pass18_engine_bad'.PHP_EOL;
