<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$thrown=false;try{\Titan\BookingIntegration\LifecycleMutationClock::reschedule(['booking_date_time'=>'2026-12-02 09:00:00'],['start_at'=>'2026-12-03 09:00:00']);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('move wrong time');
echo "PASS pass27_move_time_rejected\n";
