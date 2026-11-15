<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/validate_fixture.php';
$file=tempnam(sys_get_temp_dir(),'fixture');file_put_contents($file,'[]');try{Pass40Fixture::inspect($file,'titan_booking_test_acceptance');throw new Exception('must fail');}catch(InvalidArgumentException $e){}finally{unlink($file);}
echo 'PASS 20_fixture_rejects_nonobject'.PHP_EOL;
