<?php
declare(strict_types=1);namespace App\Booking\Public\Intent;
final class IntentIssuer{public static function issue(string $host,int $owner,array $request,string $secret,int $now):string{if($owner<1)throw new \InvalidArgumentException('Invalid business');$data=['v'=>1,'host'=>IntentHost::require($host),'owner'=>$owner,'selection'=>IntentInput::parse($request),'iat'=>$now,'exp'=>IntentClock::expiry($now),'nonce'=>IntentNonce::create(),'scope'=>'preview_only'];$body=IntentCodec::encode($data);return $body.'.'.IntentSigner::sign($body,$secret);}}
