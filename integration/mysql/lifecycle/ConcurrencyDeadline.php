<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyDeadline {
 public static function seconds(int $timeoutMs):float {if($timeoutMs<100||$timeoutMs>120000)throw new \InvalidArgumentException('Unsafe worker timeout');return $timeoutMs/1000;}
 public static function expired(float $start,int $timeoutMs,float $now):bool {return ($now-$start)>=self::seconds($timeoutMs);}
}
