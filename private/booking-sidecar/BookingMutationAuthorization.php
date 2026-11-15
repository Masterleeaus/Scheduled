<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Reject unauthenticated and incorrectly owned booking mutation attempts. */
final class BookingMutationAuthorization {
 public static function assertActor(array $actor,int $clientId):void {
  $actor=BookingMutationActor::normalize($actor);
  if($clientId<1)throw new \DomainException('Booking has no valid customer owner');
  if($actor['role']==='customer' && $actor['id']!==$clientId)throw new \DomainException('Booking is not owned by customer');
 }
}
