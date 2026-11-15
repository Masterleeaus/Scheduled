<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
assert(IntentSecret::fromConfig(str_repeat('a',32))===str_repeat('a',32));expectException(fn()=>IntentSecret::fromConfig('small'));
echo 'PASS pass33_secret\n';
