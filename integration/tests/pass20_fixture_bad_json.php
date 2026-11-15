<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$tmp=tempnam(sys_get_temp_dir(),'fixture-');file_put_contents($tmp,'not-json');try{expectFailure(fn()=>LifecycleFixtureLoader::read($tmp));}finally{unlink($tmp);}
