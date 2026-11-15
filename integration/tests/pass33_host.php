<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
assert(IntentHost::require('Example.COM')==='example.com');expectException(fn()=>IntentHost::require('localhost'));
echo 'PASS pass33_host\n';
