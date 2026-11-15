<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit('CLI only');}
require dirname(__DIR__).'/lifecycle/bootstrap.php';
foreach(['AcceptanceWindowIsolation','AcceptanceFixturePaths','AcceptanceScopeGate'] as $name)require_once __DIR__.'/'.$name.'.php';
use Titan\BookingIntegration\{AcceptanceFixturePaths,AcceptanceScopeGate,LifecycleFixtureLoader,LifecycleFailureRedactor};
try {
 $env=$_ENV+getenv();
 $paths=AcceptanceFixturePaths::fromEnvironment($env);
 $lifecycle=LifecycleFixtureLoader::read($paths['lifecycle']);
 $concurrency=LifecycleFixtureLoader::read($paths['concurrency']);
 AcceptanceScopeGate::assert($env,$lifecycle,$concurrency);
 echo "PASS64: Both fixtures validated, disposable scope approved, booking windows distinct. No database touched.\n";
} catch(Throwable $e){fwrite(STDERR,'BLOCKED: '.LifecycleFailureRedactor::message($e).PHP_EOL);exit(78);}
