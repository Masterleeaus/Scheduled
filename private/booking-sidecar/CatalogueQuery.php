<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueQuery {public static function rows(\PDO $db,string $sql,int $limit):array {$statement=$db->prepare($sql);if($statement===false||$statement->execute()!==true)throw new \RuntimeException('Catalogue query failed');$rows=$statement->fetchAll(\PDO::FETCH_ASSOC);if(!is_array($rows))throw new \RuntimeException('Catalogue rows unavailable');if(count($rows)>$limit)throw new \RuntimeException('Catalogue exceeds safe limit');return $rows;}}
