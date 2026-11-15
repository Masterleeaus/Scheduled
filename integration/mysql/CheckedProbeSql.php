<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Fail closed when PDO is configured for silent errors, even in acceptance probes. */
final class CheckedProbeSql {
 public static function query(\PDO $db,string $sql):\PDOStatement {
  $statement=$db->query($sql);
  if(!$statement instanceof \PDOStatement)throw new \RuntimeException('Database probe query failed');
  return $statement;
 }
 public static function execute(\PDO $db,string $sql,array $params=[]):\PDOStatement {
  $statement=$db->prepare($sql);
  if(!$statement instanceof \PDOStatement)throw new \RuntimeException('Database probe preparation failed');
  if($statement->execute($params)!==true)throw new \RuntimeException('Database probe execution failed');
  return $statement;
 }
 public static function scalar(\PDO $db,string $sql,array $params=[]):mixed {
  $statement=$params===[]?self::query($db,$sql):self::execute($db,$sql,$params);
  $value=$statement->fetchColumn();
  if($value===false||$value===null)throw new \RuntimeException('Database probe returned no scalar');
  return $value;
 }
 public static function rows(\PDO $db,string $sql):array {
  $rows=self::query($db,$sql)->fetchAll(\PDO::FETCH_ASSOC);
  if(!is_array($rows))throw new \RuntimeException('Database probe failed to fetch rows');
  return $rows;
 }
}
