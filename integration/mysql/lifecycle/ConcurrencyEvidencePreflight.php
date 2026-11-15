<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyEvidencePreflight {
 public static function assert(\PDO $db,string $database):void {
  if($db->getAttribute(\PDO::ATTR_DRIVER_NAME)!=='mysql')throw new \RuntimeException('MySQL required for concurrency evidence');
  $statement=$db->query('SELECT DATABASE()');if($statement===false)throw new \RuntimeException('Cannot inspect evidence database');$actual=$statement->fetchColumn();if(!is_string($actual))throw new \RuntimeException('Invalid evidence database identity');if($actual!==$database)throw new \RuntimeException('Evidence connection database mismatch');
 }
}
