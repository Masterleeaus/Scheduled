<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
check(LifecycleBookingResult::created(['status'=>'active','replayed'=>false,'booking_id'=>3])===3);
