<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCanonicalPayload,BookingKey,BookingIdempotency,BookingLedgerState,BookingPositiveId,BookingLegacyDuration,BookingBusyWindow,BookingMutationReplay,BookingSubmissionSafety,BookingMutationActor,BookingCustomer};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $f):void {try{$f();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Expected validation failure');}
$a=BookingBusyWindow::fromRecords([['start'=>'2027-01-02 10:00:00','duration_minutes'=>'90']],'Australia/Melbourne');check(count($a)===1&&$a[0][1]-$a[0][0]===5400,'busy length');
echo 'PASS pass28_busy_duration_accept'.PHP_EOL;
