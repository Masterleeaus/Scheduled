<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingLocalTimeResolution,BookingWallClockDuration,BookingConflictHorizon,BookingConflictQueryWindow,BookingTemporalConsistency,BookingSlotEvidenceGuard,BookingMutationTimeGuard,BookingTimeZonePolicy,BookingTemporalReleaseGate,BookingTestExitPolicy,BookingTimeWindow};
function ok($v):void{if(!$v)throw new RuntimeException('assertion failed');}
function deny(callable $f):void{try{$f();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('expected rejection');}
$v=BookingTimeWindow::parse('2026-10-04 01:45:00',60,'Australia/Melbourne');ok($v['end']-$v['start']===3600&&$v['local_end']==='2026-10-04 03:45:00');
echo 'PASS pass17 window_elapsed'.PHP_EOL;
