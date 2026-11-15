<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{MysqlTestConfig,DisposableName,MysqlDriverCheck,SchemaInventory,TransactionalTableAudit,AcceptanceReport,ConcurrentAttemptPolicy};
function ok(bool $ok):void{if(!$ok)throw new RuntimeException('Assertion failed');}
function deny(callable $f):void{try{$f();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$s=file_get_contents(__DIR__.'/../mysql/run_acceptance.php');ok(!preg_match('/\b(DROP|TRUNCATE|DELETE|INSERT|UPDATE)\s+(TABLE|FROM|INTO|ct_)/i',$s));
echo 'PASS pass18_no_destructive_sql'.PHP_EOL;
