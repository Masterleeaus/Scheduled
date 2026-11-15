<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/validate_fixture.php';
$file=tempnam(sys_get_temp_dir(),'fixture');file_put_contents($file,json_encode(['database'=>'titan_booking_test_acceptance','environment'=>'production','tenant'=>[]]));try{Pass40Fixture::inspect($file,'titan_booking_test_acceptance');throw new Exception('must fail');}catch(DomainException $e){}finally{unlink($file);}
echo 'PASS 06_fixture_not_disposable'.PHP_EOL;
