<?php
declare(strict_types=1);namespace App\Booking\Public\Intent;
use App\Booking\Public\PublicHost;
final class IntentHost{public static function require(string $host):string{return PublicHost::normalize($host);}}
