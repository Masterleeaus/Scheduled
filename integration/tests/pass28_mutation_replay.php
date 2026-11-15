<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCanonicalPayload,BookingKey,BookingIdempotency,BookingLedgerState,BookingPositiveId,BookingLegacyDuration,BookingBusyWindow,BookingMutationReplay,BookingSubmissionSafety,BookingMutationActor,BookingCustomer};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $f):void {try{$f();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Expected validation failure');}
check(BookingMutationReplay::evaluate(['fingerprint'=>str_repeat('a',64),'state'=>'completed','booking_id'=>'12'],str_repeat('a',64))===12,'mutation replay');rejects(fn()=>BookingMutationReplay::evaluate(['fingerprint'=>str_repeat('a',64),'state'=>'completed','booking_id'=>'12oops'],str_repeat('a',64)));
echo 'PASS pass28_mutation_replay'.PHP_EOL;
