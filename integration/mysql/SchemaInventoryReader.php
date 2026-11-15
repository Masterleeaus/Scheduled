<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class SchemaInventoryReader {
 public static function read(\PDO $pdo):array {
  $inventory=[];
  foreach(CheckedProbeSql::rows($pdo,'SELECT TABLE_NAME,COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE()') as $row)$inventory[$row['TABLE_NAME']][]=$row['COLUMN_NAME'];return $inventory;
 }
}
