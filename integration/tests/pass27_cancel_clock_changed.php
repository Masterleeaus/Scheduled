<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$thrown=false;try{\Titan\BookingIntegration\LifecycleMutationClock::cancellation(['booking_date_time'=>'2026-12-01 09:00:00'],['booking_date_time'=>'2026-12-01 10:00:00']);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('cancel moved time');
echo "PASS pass27_cancel_clock_changed\n";
