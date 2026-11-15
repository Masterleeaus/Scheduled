<?php
use App\Booking\Public\Intent\{IntentField,IntentClock,IntentNonce,IntentHost,IntentSchedule,IntentInput,IntentCodec,IntentSigner,IntentIssuer,IntentVerifier,IntentResponse,IntentSecret};
require __DIR__.'/pass33_bootstrap.php';
$v=IntentInput::parse(fixture());assert($v['quantity']===2);$bad=fixture();$bad['tenant_id']=1;expectException(fn()=>IntentInput::parse($bad));
echo 'PASS pass33_input\n';
