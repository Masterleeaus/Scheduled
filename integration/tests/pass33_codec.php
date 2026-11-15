<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
$x=IntentCodec::encode(['v'=>1]);assert(IntentCodec::decode($x)['v']===1);expectException(fn()=>IntentCodec::decode('%'));
echo 'PASS pass33_codec\n';
