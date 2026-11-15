<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$a=[2=>['id'=>2,'booking_status'=>'A']];$b=[2=>['id'=>2,'booking_status'=>'CC']];$thrown=false;try{\Titan\BookingIntegration\LifecycleSiblingIntegrity::verify($a,$b);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('mutation accepted');
echo "PASS pass27_sibling_changed\n";
