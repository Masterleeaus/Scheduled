<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/validate_fixture.php';
$file=tempnam(sys_get_temp_dir(),'fixture');file_put_contents($file,json_encode(['database'=>'titan_booking_test_acceptance','environment'=>'disposable','tenant'=>['owner'=>1]]));try{if(!Pass40Fixture::inspect($file,'titan_booking_test_acceptance')['valid'])throw new Exception('invalid');}finally{unlink($file);}
echo 'PASS 07_fixture_accepts_scoped'.PHP_EOL;
