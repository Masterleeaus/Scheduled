<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$x=validFixture();LifecyclePreflight::inspect(['BOOKING_LIFECYCLE_ACK'=>LifecycleConsent::TOKEN],['database'=>'titan_booking_test_alpha'],$x);
