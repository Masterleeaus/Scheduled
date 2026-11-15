<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
expectFailure(fn()=>LifecycleBookingResult::replay(['replayed'=>true,'booking_id'=>4],3));
