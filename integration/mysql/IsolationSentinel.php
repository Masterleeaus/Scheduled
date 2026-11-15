<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class IsolationSentinel {
 public static function assert(\PDO $pdo,string $database):void {
  DisposableName::assert($database);$actual=(string)CheckedProbeSql::scalar($pdo,'SELECT DATABASE()');if($actual!==$database)throw new \DomainException('Database changed during testing');
 }
}
