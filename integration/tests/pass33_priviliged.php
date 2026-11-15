<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
$a=fixture();$a['payment']=true;expectException(fn()=>IntentInput::parse($a));
echo 'PASS pass33_priviliged\n';
