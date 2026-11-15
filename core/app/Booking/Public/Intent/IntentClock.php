<?php
declare(strict_types=1); namespace App\Booking\Public\Intent;
final class IntentClock { public static function now():int {return time();} public static function expiry(int $now,int $ttl=600):int {if($now<=0||$ttl<60||$ttl>900)throw new \InvalidArgumentException('Invalid intent expiry');return $now+$ttl;} }
