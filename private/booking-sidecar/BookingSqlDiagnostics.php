<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Non-sensitive schema and capability summary suitable for CI/CLI output. */
final class BookingSqlDiagnostics {
 public static function summary(array $schema,array $capability,array $requirements):array {
  return ['schema_ok'=>($schema['ok']??false)===true,
   'missing_columns'=>$schema['missing']??[],
   'database_ref'=>$capability['database_ref']??null,
   'transactional_bookings'=>($capability['transactional_engine']??false)===true,
   'integration_ready'=>($requirements['ready']??false)===true,
   'missing_requirements'=>$requirements['missing']??[]];
 }
}
