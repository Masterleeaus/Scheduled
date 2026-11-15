<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$thrown=false;try{\Titan\BookingIntegration\LifecycleMutationStatus::rescheduled(['booking_status'=>'CC'],['booking_status'=>'RS']);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('cancelled move accepted');
echo "PASS pass27_reschedule_status_bad\n";
