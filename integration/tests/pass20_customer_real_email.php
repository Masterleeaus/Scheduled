<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$x=validFixture()['create'];$x['customer']['email']='real@example.com';expectFailure(fn()=>LifecycleCustomerSafety::assert($x));
