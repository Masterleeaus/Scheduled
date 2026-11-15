<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
assert(IntentNonce::valid(IntentNonce::create()));assert(!IntentNonce::valid('test'));
echo 'PASS pass33_nonce\n';
