<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class SchemaFailureReport {
 public static function fromInventories(array $columns,array $engines):array {
  $missing=SchemaInventory::missing($columns);$bad=TransactionalTableAudit::bad($engines);
  return ['valid'=>count($missing)===0&&count($bad)===0,'missing'=>$missing,'non_innodb'=>$bad];
 }
}
