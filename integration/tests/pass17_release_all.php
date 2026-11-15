<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingLocalTimeResolution,BookingWallClockDuration,BookingConflictHorizon,BookingConflictQueryWindow,BookingTemporalConsistency,BookingSlotEvidenceGuard,BookingMutationTimeGuard,BookingTimeZonePolicy,BookingTemporalReleaseGate,BookingTestExitPolicy,BookingTimeWindow};
function ok($v):void{if(!$v)throw new RuntimeException('assertion failed');}
function deny(callable $f):void{try{$f();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('expected rejection');}
$r=BookingTemporalReleaseGate::evaluate(array_fill_keys(['dst_gap_rejection','dst_repeat_rejection','duration_elapsed','overlap_lookback','future_slot_check'],true));ok($r['ready']);
echo 'PASS pass17 release_all'.PHP_EOL;
