<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedOverlapReader {
 public static function read(\PDO $db,array $window):array {
  $stmt=$db->prepare(PersistedOverlapQuery::SQL);if(!$stmt)throw new \RuntimeException('Cannot prepare overlap scan');
  if(!$stmt->execute(PersistedOverlapQuery::bounds($window)))throw new \RuntimeException('Cannot run overlap scan');
  $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);PersistedScanPolicy::assertBounded($rows);return $rows;
 }
}
