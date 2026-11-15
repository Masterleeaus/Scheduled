<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
assert(IntentClock::expiry(1000)===1600);expectException(fn()=>IntentClock::expiry(10,10));
echo 'PASS pass33_clock\n';
