<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;
/** Defensive read against the original Cleanto appointment/order tables. */
final class BookingConflictReader {
 public function __construct(private PDO $db){}
 public function busy(int $staffId,string $from,string $until,string $zone):array {
  [$lower,$upper]=BookingConflictRequestBounds::validate($staffId,$from,$until,$zone);
  $lookback=BookingConflictHorizon::lowerBound($lower);
  $stmt=$this->db->prepare(BookingConflictReadbackSql::query());
  if($stmt===false||$stmt->execute([$lookback,$until,(string)$staffId])!==true)throw new \RuntimeException('Conflict query failed');
  $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
  if(!is_array($rows))throw new \RuntimeException('Conflict scan unavailable');
  if(count($rows)>5000)throw new \DomainException('Conflict scan truncated');
  return BookingConflictRows::occupied($rows,$staffId,$zone,$lower->getTimestamp(),$upper->getTimestamp());
 }
}
