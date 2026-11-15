<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{MysqlTestConfig,DisposableName,MysqlDriverCheck,SchemaInventory,TransactionalTableAudit,AcceptanceReport,ConcurrentAttemptPolicy};
function ok(bool $ok):void{if(!$ok)throw new RuntimeException('Assertion failed');}
function deny(callable $f):void{try{$f();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$x=MysqlTestConfig::fromEnvironment(['BOOKING_TEST_HOST'=>'127.0.0.1','BOOKING_TEST_DATABASE'=>'titan_booking_test_ci','BOOKING_TEST_USER'=>'ci']);ok($x['database']==='titan_booking_test_ci');
echo 'PASS pass18_config_valid'.PHP_EOL;
