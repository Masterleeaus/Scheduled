<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingIntegration\{MysqlDriverCheck,MysqlTestConfig,MysqlTestConnection,LifecycleFixtureLoader,LifecyclePreflight,LifecycleRunner,LifecycleFailureRedactor,AcceptanceExitCode};
if(PHP_SAPI!=='cli'){http_response_code(403);exit('CLI only');}
if(!MysqlDriverCheck::available()){fwrite(STDERR,"SKIP: pdo_mysql unavailable; no booking was created
");exit(AcceptanceExitCode::SKIP);}
try {
 $env=$_ENV+getenv(); $cfg=MysqlTestConfig::fromEnvironment($env);
 $path=$env['BOOKING_LIFECYCLE_FIXTURE']??'';
 if(!is_string($path)||$path==='')throw new InvalidArgumentException('BOOKING_LIFECYCLE_FIXTURE required');
 $fixture=LifecycleFixtureLoader::read($path);LifecyclePreflight::inspect($env,$cfg,$fixture);
 $db=MysqlTestConnection::open($cfg);
 $result=LifecycleRunner::run($db,$cfg,$fixture);
 echo json_encode($result,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR).PHP_EOL;
 exit(AcceptanceExitCode::PASS);
}catch(Throwable $e){fwrite(STDERR,'FAIL: '.LifecycleFailureRedactor::message($e).PHP_EOL);exit(AcceptanceExitCode::FAIL);}
