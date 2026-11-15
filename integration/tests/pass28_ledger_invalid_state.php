<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCanonicalPayload,BookingKey,BookingIdempotency,BookingLedgerState,BookingPositiveId,BookingLegacyDuration,BookingBusyWindow,BookingMutationReplay,BookingSubmissionSafety,BookingMutationActor,BookingCustomer};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $f):void {try{$f();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Expected validation failure');}
rejects(fn()=>BookingIdempotency::decide(['fingerprint'=>str_repeat('a',64),'state'=>'unknown'],str_repeat('a',64)));
echo 'PASS pass28_ledger_invalid_state'.PHP_EOL;
