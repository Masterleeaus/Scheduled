<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** information_schema reader uses server schema selection; no caller-provided table names. */
final class BookingSchemaColumnsReader {
 public function __construct(private \PDO $db){}
 public function columns(string $table):array {
  $allowed=array_keys(BookingSchemaInspector::expected());$allowed[]=BookingLedgerSchemaContract::TABLE;
  if(!in_array($table,$allowed,true))throw new \InvalidArgumentException('Unsupported schema table');
  $q=$this->db->prepare('SELECT column_name FROM information_schema.columns WHERE table_catalog=current_database() AND table_schema=current_schema() AND table_name=? ORDER BY ordinal_position');
  $qRows=null;if($q===false||$q->execute([$table])!==true)throw new \RuntimeException('Schema column lookup failed');$qRows=$q->fetchAll(\PDO::FETCH_COLUMN);if(!is_array($qRows))throw new \RuntimeException('Schema lookup unavailable');return array_map('strval',$qRows);
 }
}
