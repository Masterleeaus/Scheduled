<?php
declare(strict_types=1); namespace App\Booking\Public\Intent;
final class IntentNonce {public static function create():string{return bin2hex(random_bytes(16));}public static function valid(mixed $v):bool{return is_string($v)&&(bool)preg_match('/^[0-9a-f]{32}$/D',$v);}}
