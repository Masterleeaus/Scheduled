<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** A separate PDO session must independently verify the committed winning appointment, order and customer linkage. */
final class ConcurrencyDatabaseEvidence {
 public static function verify(\PDO $db,int $winner,array $fixture):array {
  $identity=ConcurrencyFixtureIdentity::from($fixture);
  $row=ConcurrencyBookingReadback::read($db,$winner);
  ConcurrencyReadbackPolicy::assert($row,$winner,$identity);
  $full=ConcurrencyFullReadbackEvidence::verify($db,$winner,$fixture);
  return ['persisted'=>true,'booking_id'=>$winner,'verified_service_id'=>$identity['service_id'],
          'verified_staff_count'=>count($identity['staff_ids'])]+$full;
 }
}
