<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/mysql/lifecycle/bootstrap.php';
foreach(['AcceptanceWindowIsolation','AcceptanceFixturePaths','AcceptanceScopeGate'] as $name)require_once dirname(__DIR__,2).'/mysql/pass64/'.$name.'.php';
use Titan\BookingIntegration\{AcceptanceScopeGate,AcceptanceWindowIsolation,AcceptanceFixturePaths,AcceptanceTestSettings,LifecycleFixtureLoader};
$fixture=LifecycleFixtureLoader::read(dirname(__DIR__,2).'/mysql/lifecycle/fixture.example.json');
$second=$fixture;$second['create']['start_at']='2027-01-12 09:00:00';
$env=['BOOKING_TEST_HOST'=>'localhost','BOOKING_TEST_DATABASE'=>'titan_booking_test_acceptance','BOOKING_TEST_USER'=>'testing', 'BOOKING_LIFECYCLE_ACK'=>'I_UNDERSTAND_THIS_WRITES_TEST_DATA','BOOKING_ACCEPT_TEST_ONLY'=>'YES_DISPOSABLE_ONLY'];
$passes=0;
function check64(bool $pass,string $message):void{global $passes;if(!$pass)throw new \RuntimeException('Failed: '.$message);$passes++;}
function blocks64(callable $f,string $message):void{try{$f();}catch(\DomainException|\InvalidArgumentException $e){check64(true,$message);return;}check64(false,$message);}
check64(AcceptanceScopeGate::assert($env,$fixture,$second)['database']==='titan_booking_test_acceptance','valid fixture pair');
blocks64(fn()=>AcceptanceScopeGate::assert(array_diff_key($env,['BOOKING_LIFECYCLE_ACK'=>1]),$fixture,$second),'missing consent');
blocks64(fn()=>AcceptanceScopeGate::assert(array_diff_key($env,['BOOKING_ACCEPT_TEST_ONLY'=>1]),$fixture,$second),'missing test flag');
blocks64(fn()=>AcceptanceScopeGate::assert($env+[],array_merge($fixture,['database'=>'production']),$second),'production fixture');
blocks64(fn()=>AcceptanceScopeGate::assert(array_merge($env,['BOOKING_TEST_DATABASE'=>'production']),$fixture,$second),'unsafe database');
blocks64(fn()=>AcceptanceScopeGate::assert(array_merge($env,['BOOKING_TEST_HOST'=>'external.example']),$fixture,$second),'unsafe database host');
blocks64(fn()=>AcceptanceWindowIsolation::assert($fixture,$fixture),'same window');
$overlap=$second;$overlap['create']['start_at']='2027-01-10 09:30:00';
blocks64(fn()=>AcceptanceWindowIsolation::assert($fixture,$overlap),'partial overlap');
$adjoining=$second;$adjoining['create']['start_at']='2027-01-10 10:00:00';
AcceptanceWindowIsolation::assert($fixture,$adjoining);check64(true,'adjacent windows allowed');
$zone=$second;$zone['create']['timezone']='UTC';blocks64(fn()=>AcceptanceWindowIsolation::assert($fixture,$zone),'timezone mismatch');
$invalid=$second;$invalid['create']['start_at']='bad-date';blocks64(fn()=>AcceptanceWindowIsolation::assert($fixture,$invalid),'bad date');
$invalid=$second;$invalid['create']['duration_minutes']=0;blocks64(fn()=>AcceptanceWindowIsolation::assert($fixture,$invalid),'bad duration');
$invalid=$second;$invalid['create']['customer']['email']='real@example.com';blocks64(fn()=>AcceptanceScopeGate::assert($env,$fixture,$invalid),'real customer blocked');
blocks64(fn()=>AcceptanceFixturePaths::fromEnvironment([]),'fixture paths required');
echo "PASS64 gates: $passes/14 passed\n";
