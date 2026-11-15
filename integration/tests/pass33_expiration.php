<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
$s=str_repeat('x',32);$t=IntentIssuer::issue('example.com',4,fixture(),$s,1000);expectException(fn()=>IntentVerifier::verify($t,'example.com',4,$s,1600));
echo 'PASS pass33_expiration\n';
