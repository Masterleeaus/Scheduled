<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Replay result must have a positive committed booking id; never assume status. */
final class BookingReplayPayloadGuard {
 public static function bookingId(array $row):int {
  if(($row['state']??null)!=='completed')throw new \DomainException('Replay not completed');
  $id=filter_var($row['booking_id']??null,FILTER_VALIDATE_INT);
  if($id===false||$id<1||$id>2147483647)throw new \DomainException('Replay has no committed booking');
  return (int)$id;
 }
}
