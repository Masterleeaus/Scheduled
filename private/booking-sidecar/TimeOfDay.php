<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
final class TimeOfDay {
 public static function parse(string $time): int {
  if (!preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/D',$time,$m)) throw new InvalidArgumentException('Invalid time');
  return (int)$m[1]*3600+(int)$m[2]*60+(int)$m[3];
 }
 public static function format(int $seconds): string {
  if ($seconds<0 || $seconds>=86400) throw new InvalidArgumentException('Invalid seconds');
  return sprintf('%02d:%02d:%02d',intdiv($seconds,3600),intdiv($seconds%3600,60),$seconds%60);
 }
}
