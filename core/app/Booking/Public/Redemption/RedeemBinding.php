<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemBinding { public static function require(array $intent,string $host,int $owner):void {if(($intent['host']??null)!==$host||($intent['owner']??null)!==$owner||($intent['scope']??null)!=='preview_only')throw new \InvalidArgumentException('Business binding mismatch');} }
