<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Separate MySQL server sessions are required for independent commit readback. */
final class LifecycleConnectionPair {
 public static function assert(\PDO $writer,\PDO $reader):void {
  if($writer===$reader)throw new \RuntimeException('Acceptance reader must use independent connection');
  $a=CheckedProbeSql::scalar($writer,'SELECT CONNECTION_ID()');
  $b=CheckedProbeSql::scalar($reader,'SELECT CONNECTION_ID()');
  if(!self::valid($a)||!self::valid($b)||(string)$a===(string)$b)throw new \RuntimeException('Acceptance sessions are not independent');
 }
 private static function valid(mixed $id):bool {
  if(!is_int($id)&&!is_string($id))return false;
  return preg_match('/^[1-9][0-9]{0,19}$/D',(string)$id)===1;
 }
}
