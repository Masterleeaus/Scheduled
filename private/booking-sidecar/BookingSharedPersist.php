<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Reuse Cleanto writers without beginning or committing another transaction. */
final class BookingSharedPersist {
 public static function write(\PDO $db,array $command,string $rate):int {
  BookingAtomicPersistBoundary::assert($db);
  $order=(new BookingOrderSequence($db))->next();
  BookingAtomicPersistBoundary::assert($db);
  $client=(new BookingLegacyClientWriter($db))->insert($order,$command['customer'],$command['duration_minutes'],$command['personal_info']??[]);
  BookingAtomicPersistBoundary::assert($db);
  $id=(new BookingLegacyAppointmentWriter($db))->insert($order,$client,$command,$rate);
  BookingAtomicPersistBoundary::assert($db);
  return $id;
 }
}
