<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConnectionPair {
 public static function create(array $cfg):array {
  $a=MysqlTestConnection::open($cfg);$b=MysqlTestConnection::open($cfg);
  $database=$cfg['database'];IsolationSentinel::assert($a,$database);IsolationSentinel::assert($b,$database);
  if((int)CheckedProbeSql::scalar($a,'SELECT CONNECTION_ID()')===(int)CheckedProbeSql::scalar($b,'SELECT CONNECTION_ID()'))throw new \RuntimeException('Distinct connections required');
  return [$a,$b];
 }
}
