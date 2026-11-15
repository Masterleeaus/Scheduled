<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class MysqlTestConnection {
 public static function open(array $cfg):\PDO{
  MysqlDriverCheck::requireAvailable();DisposableName::assert($cfg['database']);
  $host=$cfg['host'];if(!in_array($host,['127.0.0.1','localhost','mysql-test','mariadb-test'],true))throw new \DomainException('Unapproved test host');
  $pdo=new \PDO('mysql:host='.$host.';dbname='.$cfg['database'].';charset=utf8mb4',$cfg['username'],$cfg['password'],[\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION,\PDO::ATTR_EMULATE_PREPARES=>false]);
  $actual=(string)CheckedProbeSql::scalar($pdo,'SELECT DATABASE()');if($actual!==$cfg['database'])throw new \RuntimeException('Connected to wrong database');return $pdo;
 }
}
