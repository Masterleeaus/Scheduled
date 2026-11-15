<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
expectFailure(fn()=>LifecycleTestScope::assert(['database'=>'titan_booking_test_alpha'],['database'=>'titan_booking_test_beta','environment'=>'disposable']));
