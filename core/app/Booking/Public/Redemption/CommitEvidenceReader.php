<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitEvidenceReader { public static function read(\PDO $db,int $id):array {
 if($id<1)throw new \InvalidArgumentException('Invalid booking');
 CommitTransactionBoundary::assert($db);
 $s=$db->prepare('SELECT id, client_id, order_id, booking_date_time, booking_status FROM ct_bookings WHERE id = ? LIMIT 2 FOR UPDATE');
 $s->execute([$id]);$rows=$s->fetchAll(\PDO::FETCH_ASSOC);if(count($rows)!==1)throw new \RuntimeException('Booking not uniquely persisted');return $rows[0];
}}
