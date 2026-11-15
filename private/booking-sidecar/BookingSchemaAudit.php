<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Read-only database schema audit. Never creates production tables. */
final class BookingSchemaAudit {
 public function __construct(private BookingSchemaColumnsReader $reader){}
 public function inspect():array {
  $actual=[];
  foreach(array_keys(BookingSchemaInspector::expected()) as $name)$actual[$name]=$this->reader->columns($name);
  $missing=BookingSchemaInspector::diff($actual);
  $ledger=$this->reader->columns(BookingLedgerSchemaContract::TABLE);
  foreach(BookingLedgerSchemaContract::diff($ledger) as $column)$missing[]=BookingLedgerSchemaContract::TABLE.'.'.$column;
  return ['ok'=>count($missing)===0,'missing'=>$missing,'checked_tables'=>count($actual)+1];
 }
}
