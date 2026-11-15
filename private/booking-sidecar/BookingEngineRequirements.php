<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Explicit prerequisites for exercising transactions against a real MySQL deployment. */
final class BookingEngineRequirements {
 public static function check(array $environment):array {
  $checks=['pdo_mysql'=>($environment['pdo_mysql']??false)===true,
   'mysql_8_or_mariadb_supported'=>($environment['server_supported']??false)===true,
   'transactional_engine'=>($environment['innodb']??false)===true,
   'isolated_test_database'=>($environment['isolated']??false)===true,
   'schema_compatible'=>($environment['schema']??false)===true,
   'cleanto_pricing_parity'=>($environment['pricing']??false)===true,
   'booking_conflict_concurrency'=>($environment['concurrency']??false)===true];
  return ['ready'=>!in_array(false,$checks,true),'checks'=>$checks,'missing'=>array_keys(array_filter($checks,static fn($v)=>!$v))];
 }
}
