<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Accept only the canonical internal command shape. */
final class BookingSharedCommand {
 public static function normalize(array $payload):array {
  BookingSubmissionSafety::assertSafe($payload);
  $command=BookingCommand::create($payload);
  BookingCreationPolicy::assertScope($payload,$command);
  BookingMutationTimeGuard::assertFuture($command['window'],time());
  if(count($command['staff_ids'])!==1)throw new \DomainException('Redemption supports one staff assignment');
  return $command;
 }
}
