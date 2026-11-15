<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Only exactly one inserted row can produce a new Cleanto booking identity. */
final class BookingAffectedRows {
 public static function inserted(\PDOStatement $statement,string $operation):void {
  if($statement->rowCount()!==1)throw new \RuntimeException($operation.' did not insert exactly one row');
 }
}
