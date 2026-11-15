<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Generate bounded, credential-free reasons for refusing a booking write. */
final class BookingLiveWriteEvidence {
 public static function assess(array $snapshot):array {
  $missing=BookingLiveWriteColumns::missing($snapshot['columns']??[]);
  $engines=BookingLiveWriteEnginePolicy::errors($snapshot['engines']??[]);
  return ['ready'=>!$missing&&!$engines,'errors'=>array_merge($missing,$engines)];
 }
}
