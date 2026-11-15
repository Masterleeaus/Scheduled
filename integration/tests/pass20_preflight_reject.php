<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$x=validFixture();expectFailure(fn()=>LifecyclePreflight::inspect([],['database'=>'titan_booking_test_alpha'],$x));
