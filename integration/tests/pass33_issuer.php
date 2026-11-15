<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
$t=IntentIssuer::issue('example.com',4,fixture(),str_repeat('x',32),1000);assert(count(explode('.',$t))===2);
echo 'PASS pass33_issuer\n';
