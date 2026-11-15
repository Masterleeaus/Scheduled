<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
$s=str_repeat('x',32);$sig=IntentSigner::sign('body',$s);IntentSigner::check('body',$sig,$s);expectException(fn()=>IntentSigner::check('evil',$sig,$s));
echo 'PASS pass33_signer\n';
