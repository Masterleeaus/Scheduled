<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyWorkerExit {
 public static function assert(int $code,string $stderr):void {
  if($code!==0)throw new \RuntimeException('Concurrency worker failed (exit '. $code .'): '.LifecycleFailureRedactor::message(new \RuntimeException(substr($stderr,0,600))));
 }
}
