<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingIntegration\{ConcurrencyBarrier,ConcurrencyResult,ConcurrencyResultStore,ConcurrencyWorkerPayload,LifecycleConsent,LifecycleTestScope,LifecycleFixtureLoader,LifecycleFixtureValidator,LifecycleCustomerSafety,LifecycleDatabaseReadiness,AcceptanceTestSettings,MysqlTestConfig,MysqlTestConnection};
if(PHP_SAPI!=='cli'||count($argv)!==4)exit(2);
[$script,$worker,$dir,$fixturePath]=$argv;
try {
 $env=$_ENV+getenv();$cfg=MysqlTestConfig::fromEnvironment($env);$fixture=LifecycleFixtureLoader::read($fixturePath);
 LifecycleConsent::assert($env);LifecycleTestScope::assert($cfg,$fixture);LifecycleFixtureValidator::validate($fixture);LifecycleCustomerSafety::assert($fixture['create']);
 $db=MysqlTestConnection::open($cfg);LifecycleDatabaseReadiness::assert($db,$cfg['database']);
 $settings=AcceptanceTestSettings::build($cfg,$fixture,$env);$key=basename($dir);
 if(!preg_match('/^titan-concurrency-([a-f0-9]{32})$/D',$key,$m))throw new RuntimeException('Invalid worker directory');
 $command=ConcurrencyWorkerPayload::build($fixture,$m[1],$worker);
 ConcurrencyBarrier::ready($dir,$worker);ConcurrencyBarrier::awaitGo($dir);
 try {$created=(new Titan\BookingSidecar\BookingWriteCoordinator($db,$settings))->create($command);$result=ConcurrencyResult::accepted(Titan\BookingIntegration\LifecycleBookingResult::created($created));}
 catch(DomainException $e){$result=ConcurrencyResult::rejected($e);}
 ConcurrencyResultStore::write($dir,$worker,$result);exit(0);
}catch(Throwable $e){fwrite(STDERR,'Worker failed: '.Titan\BookingIntegration\LifecycleFailureRedactor::message($e).PHP_EOL);exit(1);}
