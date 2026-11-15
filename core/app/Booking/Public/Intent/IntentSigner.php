<?php
declare(strict_types=1);namespace App\Booking\Public\Intent;
final class IntentSigner{public static function sign(string $body,string $secret):string{if(strlen($secret)<32)throw new \InvalidArgumentException('Booking intent secret too short');return hash_hmac('sha256',$body,$secret);}public static function check(string $body,string $signature,string $secret):void{if(!preg_match('/^[a-f0-9]{64}$/D',$signature)||!hash_equals(self::sign($body,$secret),$signature))throw new \InvalidArgumentException('Invalid booking intent signature');}}
