<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingIntegration\{MysqlDriverCheck,MysqlTestConfig,LifecycleFixtureLoader,LifecyclePreflight,ConcurrencyAcceptanceRunner,AcceptanceExitCode,LifecycleFailureRedactor};
if(PHP_SAPI!=='cli'){http_response_code(403);exit('CLI only');}
if(!MysqlDriverCheck::available()){fwrite(STDERR,'SKIP: pdo_mysql unavailable; no concurrency test executed'.PHP_EOL);exit(AcceptanceExitCode::SKIP);}
try {$env=$_ENV+getenv();$cfg=MysqlTestConfig::fromEnvironment($env);$path=$env['BOOKING_LIFECYCLE_FIXTURE']??'';
 if(!is_string($path)||$path==='')throw new InvalidArgumentException('Fixture path required');
 $fixture=LifecycleFixtureLoader::read($path);LifecyclePreflight::inspect($env,$cfg,$fixture);
 $out=ConcurrencyAcceptanceRunner::run($env,$cfg,$fixture,$path);echo json_encode($out,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR).PHP_EOL;exit(AcceptanceExitCode::PASS);
}catch(Throwable $e){fwrite(STDERR,'FAIL: '.LifecycleFailureRedactor::message($e).PHP_EOL);exit(AcceptanceExitCode::FAIL);}
