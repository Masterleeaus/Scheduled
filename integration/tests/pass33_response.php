<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
$r=IntentResponse::preview('token');assert($r['confirmed']===false&&$r['booking_writes_enabled']===false);
echo 'PASS pass33_response\n';
