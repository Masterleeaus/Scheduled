<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
assert(IntentSchedule::parse('2026-11-12T10:00:00','Australia/Melbourne')==='2026-11-12T10:00:00');expectException(fn()=>IntentSchedule::parse('2026-10-04T02:30:00','Australia/Melbourne'));
echo 'PASS pass33_schedule\n';
