<?php
declare(strict_types=1);namespace App\Booking\Public\Intent;
final class IntentSecret{public static function fromConfig(mixed $secret):string{if(!is_string($secret)||strlen($secret)<32)throw new \RuntimeException('Booking intent secret not configured');return $secret;}}
