<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Prevent applying noncanonical ledger DDL to a tenant with an existing table. */
final class BookingSchemaMigrationGuard {
 public static function evaluate(array $existingColumns):array {
  if(!$existingColumns)return ['action'=>'create_canonical','compatible'=>true];
  $missing=BookingLedgerSchemaContract::diff($existingColumns);
  return ['action'=>$missing?'manual_reconcile':'leave_unchanged','compatible'=>!$missing,'missing'=>$missing];
 }
}
