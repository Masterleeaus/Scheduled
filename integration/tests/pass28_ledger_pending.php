<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCanonicalPayload,BookingKey,BookingIdempotency,BookingLedgerState,BookingPositiveId,BookingLegacyDuration,BookingBusyWindow,BookingMutationReplay,BookingSubmissionSafety,BookingMutationActor,BookingCustomer};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $f):void {try{$f();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Expected validation failure');}
check(BookingIdempotency::decide(['fingerprint'=>str_repeat('a',64),'state'=>'pending'],str_repeat('a',64))==='pending','pending');
echo 'PASS pass28_ledger_pending'.PHP_EOL;
