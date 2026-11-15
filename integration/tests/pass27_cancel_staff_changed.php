<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$thrown=false;try{\Titan\BookingIntegration\LifecycleMutationStaff::cancellation(['staff_ids'=>[4]],['staff_ids'=>[5]]);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('cancel moved staff');
echo "PASS pass27_cancel_staff_changed\n";
