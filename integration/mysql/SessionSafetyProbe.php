<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class SessionSafetyProbe {
 public static function assert(\PDO $db):void {
  if($db->getAttribute(\PDO::ATTR_EMULATE_PREPARES)!==false)throw new \RuntimeException('Emulated SQL preparation forbidden');
  if($db->inTransaction())throw new \RuntimeException('Unexpected ambient transaction');
  $name=(string)CheckedProbeSql::scalar($db,'SELECT DATABASE()');DisposableName::assert($name);
 }
}
