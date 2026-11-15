<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemClock { public static function check(int $now,int $issued,int $expiry):void { if($now<=0||$issued>$now+30||$now>=$expiry||$expiry-$issued>900||$expiry<=$issued)throw new \InvalidArgumentException('Expired booking intent'); } }
