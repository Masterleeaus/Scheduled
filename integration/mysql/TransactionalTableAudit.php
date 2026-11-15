<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class TransactionalTableAudit {
 public static function bad(array $engines):array { $bad=[];foreach(array_keys(SchemaInventory::required()) as $table){if(strtoupper((string)($engines[$table]??''))!=='INNODB')$bad[]=$table;}return $bad;}
 public static function read(\PDO $pdo):array {$rows=[];foreach(CheckedProbeSql::rows($pdo,'SELECT TABLE_NAME,ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()') as $row)$rows[$row['TABLE_NAME']]=$row['ENGINE'];return $rows;}
}
