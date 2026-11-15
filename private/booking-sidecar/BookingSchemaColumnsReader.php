<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** information_schema reader uses server schema selection; no caller-provided table names. */
final class BookingSchemaColumnsReader {
 public function __construct(private \PDO $db){}
 public function columns(string $table):array {
  $allowed=array_keys(BookingSchemaInspector::expected());$allowed[]=BookingLedgerSchemaContract::TABLE;
  if(!in_array($table,$allowed,true))throw new \InvalidArgumentException('Unsupported schema table');
  $q=$this->db->prepare('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? ORDER BY ORDINAL_POSITION');
  $qRows=null;if($q===false||$q->execute([$table])!==true)throw new \RuntimeException('Schema column lookup failed');$qRows=$q->fetchAll(\PDO::FETCH_COLUMN);if(!is_array($qRows))throw new \RuntimeException('Schema lookup unavailable');return array_map('strval',$qRows);
 }
}
