<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Require the exact caller-owned transaction to remain active. */
final class CommitTransactionBoundary {
 public static function assert(\PDO $db):void {
  if(!$db->inTransaction())throw new \RuntimeException('Booking write outside transaction');
 }
 public static function beforeWrite(\PDO $db):void {self::assert($db);}
 public static function afterWrite(\PDO $db):void {self::assert($db);}
}
