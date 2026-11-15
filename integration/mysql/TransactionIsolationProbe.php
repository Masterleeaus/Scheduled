<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Verify isolation and capability WITHOUT writing persistent booking data. */
final class TransactionIsolationProbe {
 public static function inspect(\PDO $db):array {
  $engine=strtoupper((string)CheckedProbeSql::scalar($db,'SELECT @@default_storage_engine'));
  $isolation=(string)CheckedProbeSql::scalar($db,'SELECT @@transaction_isolation');
  if($engine!=='INNODB')throw new \RuntimeException('Default engine must be InnoDB');
  if(!in_array(strtoupper(str_replace('-',' ', $isolation)),['READ COMMITTED','REPEATABLE READ','SERIALIZABLE'],true))throw new \RuntimeException('Unsupported isolation level');
  return ['engine'=>$engine,'isolation'=>$isolation];
 }
}
