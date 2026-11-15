<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$thrown=false;try{\Titan\BookingIntegration\LifecycleMutationStatus::cancelled(['booking_status'=>'A'],['booking_status'=>'A']);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('uncommitted cancel accepted');
echo "PASS pass27_cancel_status_bad\n";
