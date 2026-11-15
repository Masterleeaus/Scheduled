<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingLocalTimeResolution,BookingWallClockDuration,BookingConflictHorizon,BookingConflictQueryWindow,BookingTemporalConsistency,BookingSlotEvidenceGuard,BookingMutationTimeGuard,BookingTimeZonePolicy,BookingTemporalReleaseGate,BookingTestExitPolicy,BookingTimeWindow};
function ok($v):void{if(!$v)throw new RuntimeException('assertion failed');}
function deny(callable $f):void{try{$f();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('expected rejection');}
$r=BookingTemporalReleaseGate::evaluate([]);ok(!$r['ready']&&count($r['missing'])===5);
echo 'PASS pass17 release_missing'.PHP_EOL;
