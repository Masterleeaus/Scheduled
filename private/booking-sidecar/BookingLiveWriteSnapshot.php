<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Only reads INFORMATION_SCHEMA for the currently selected tenant database. */
final class BookingLiveWriteSnapshot {
 public function __construct(private \PDO $db){}
 public function read():array {
  $tables=array_keys(BookingLiveWriteColumns::required());$holders=implode(',',array_fill(0,count($tables),'?'));
  $sql="SELECT TABLE_NAME,COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ($holders)";
  $q=$this->db->prepare($sql);if($q===false||$q->execute($tables)!==true)throw new \RuntimeException('Schema columns query failed');$columns=[];
  $columnRows=$q->fetchAll(\PDO::FETCH_ASSOC);if(!is_array($columnRows))throw new \RuntimeException('Schema columns unavailable');foreach($columnRows as $row)$columns[(string)$row['TABLE_NAME']][]=(string)$row['COLUMN_NAME'];
  $sql="SELECT TABLE_NAME,ENGINE FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ($holders)";
  $q=$this->db->prepare($sql);if($q===false||$q->execute($tables)!==true)throw new \RuntimeException('Schema engines query failed');$engines=[];
  $engineRows=$q->fetchAll(\PDO::FETCH_ASSOC);if(!is_array($engineRows))throw new \RuntimeException('Schema engines unavailable');foreach($engineRows as $row)$engines[(string)$row['TABLE_NAME']]=(string)$row['ENGINE'];
  return ['columns'=>$columns,'engines'=>$engines];
 }
}
