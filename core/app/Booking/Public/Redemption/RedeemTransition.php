<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemTransition { public static function canCommit(string $state):bool{return $state==='claimed';} public static function canRetry(string $state):bool{return $state==='failed';} public static function terminal(string $state):bool{return $state==='committed';} }
