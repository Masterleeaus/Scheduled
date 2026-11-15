<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$thrown=false;try{\Titan\BookingIntegration\LifecycleMutationStaff::reschedule(['staff_ids'=>[4]],['staff_ids'=>[5]]);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('move wrong staff');
echo "PASS pass27_move_staff_rejected\n";
