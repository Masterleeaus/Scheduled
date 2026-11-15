<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Server-side cancellation preflight; never interprets a request as a confirmed cancellation. */
final class BookingCancellationGuard {
 public static function inspect(array $actor,int $clientId,string $status):array {
  BookingMutationAuthorization::assertActor($actor,$clientId);
  $target=BookingCancellationPolicy::target($status,$actor['role']);
  return ['eligible'=>true,'target_status'=>$target,'preview_only'=>true];
 }
}
