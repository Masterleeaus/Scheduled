<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$tmp=tempnam(sys_get_temp_dir(),'fixture-');file_put_contents($tmp,json_encode(validFixture()));try{check(LifecycleFixtureLoader::read($tmp)['database']==='titan_booking_test_alpha');}finally{unlink($tmp);}
