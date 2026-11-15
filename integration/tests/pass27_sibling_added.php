<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$a=[2=>['id'=>2]];$b=[2=>['id'=>2],3=>['id'=>3]];$thrown=false;try{\Titan\BookingIntegration\LifecycleSiblingIntegrity::verify($a,$b);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('addition accepted');
echo "PASS pass27_sibling_added\n";
