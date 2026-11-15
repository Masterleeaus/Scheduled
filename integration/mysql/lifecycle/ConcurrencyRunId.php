<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyRunId {
 public static function make():string {return bin2hex(random_bytes(16));}
 public static function assert(string $value):void {if(!preg_match('/^[a-f0-9]{32}$/D',$value))throw new \InvalidArgumentException('Invalid concurrency run id');}
}
