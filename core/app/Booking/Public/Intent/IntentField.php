<?php
declare(strict_types=1); namespace App\Booking\Public\Intent;
final class IntentField { public static function id(mixed $id):int { if(!is_int($id)&&!(is_string($id)&&preg_match('/^[1-9][0-9]{0,8}$/D',$id)))throw new \InvalidArgumentException('Invalid catalogue identifier');$n=(int)$id;if($n<1||$n>999999999)throw new \InvalidArgumentException('Invalid identifier');return $n; } }
