<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$x=validFixture();$x['tenant']['authenticated_owner_id']=0;expectFailure(fn()=>LifecycleFixtureValidator::validate($x));
