<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitLegacyEvidenceReader {
 public static function read(\PDO $db,int $id):array {
  if($id<1)throw new \InvalidArgumentException('Invalid booking');
  CommitTransactionBoundary::assert($db);
  $s=$db->prepare('SELECT id, client_id, order_id, service_id, method_id, method_unit_id, method_unit_qty, staff_ids, booking_date_time, booking_status FROM ct_bookings WHERE id=? LIMIT 2 FOR UPDATE');
  if($s===false||$s->execute([$id])!==true)throw new \RuntimeException('Booking evidence query failed');
  $rows=$s->fetchAll(\PDO::FETCH_ASSOC);
  if(!is_array($rows))throw new \RuntimeException('Booking evidence read failed');
  if(count($rows)!==1)throw new \RuntimeException('Expected exactly one persisted booking');
  return $rows[0];
 }
}
