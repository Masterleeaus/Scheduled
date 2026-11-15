<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Fail-closed, bounded and checked lookup for committed acceptance evidence. */
final class AcceptanceReadbackSql {
 public static function exactlyOne(\PDO $db,string $sql,array $params,string $label):array {
  if(!preg_match('/\bLIMIT\s+2\s*$/i',trim($sql)))throw new \LogicException('Readback query must be bounded to two rows');
  $statement=$db->prepare($sql);
  if($statement===false || $statement->execute($params)!==true)throw new \RuntimeException($label.' readback query failed');
  $rows=$statement->fetchAll(\PDO::FETCH_ASSOC);
  return LifecycleReadbackRows::exactlyOne($rows,$label);
 }
}
