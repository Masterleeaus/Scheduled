<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class RollbackProbe {
 public static function run(\PDO $pdo):bool {
  if($pdo->inTransaction())throw new \LogicException('Rollback probe requires clean connection');
  if($pdo->beginTransaction()!==true)throw new \RuntimeException('Cannot start rollback probe');
  try {if((int)CheckedProbeSql::scalar($pdo,'SELECT 1')!==1)throw new \RuntimeException('Database read failed');}
  finally {if($pdo->inTransaction()&&$pdo->rollBack()!==true)throw new \RuntimeException('Rollback probe failed');}
  if($pdo->inTransaction())throw new \RuntimeException('Rollback probe left open transaction');return true;
 }
}
