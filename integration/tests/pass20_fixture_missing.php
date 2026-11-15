<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
expectFailure(fn()=>LifecycleFixtureValidator::validate(['database'=>'titan_booking_test_alpha']));
