<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCanonicalPayload,BookingKey,BookingIdempotency,BookingLedgerState,BookingPositiveId,BookingLegacyDuration,BookingBusyWindow,BookingMutationReplay,BookingSubmissionSafety,BookingMutationActor,BookingCustomer};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $f):void {try{$f();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Expected validation failure');}
$x=['staff_ids'=>[3,8],'customer'=>['email'=>'a@b.com','name'=>'X']];$y=['customer'=>['name'=>'X','email'=>'a@b.com'],'staff_ids'=>[3,8]];check(BookingKey::fingerprint($x)===BookingKey::fingerprint($y),'canonical fingerprint');
echo 'PASS pass28_canonical_stability'.PHP_EOL;
