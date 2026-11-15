<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCanonicalPayload,BookingKey,BookingIdempotency,BookingLedgerState,BookingPositiveId,BookingLegacyDuration,BookingBusyWindow,BookingMutationReplay,BookingSubmissionSafety,BookingMutationActor,BookingCustomer};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $f):void {try{$f();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Expected validation failure');}
foreach(['tenant_id','owner_id','payment_status','price','booking_writes_enabled'] as $name)rejects(fn()=>BookingSubmissionSafety::assertSafe([$name=>false]));
echo 'PASS pass28_submission_privileges'.PHP_EOL;
