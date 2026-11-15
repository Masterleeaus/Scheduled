<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemVerify { public static function token(string $token,string $host,int $owner,string $secret,int $now):array { $intent=\App\Booking\Public\Intent\IntentVerifier::verify($token,$host,$owner,$secret,$now);RedeemBinding::require($intent,$host,$owner);RedeemClock::check($now,$intent['iat'],$intent['exp']);return ['key'=>RedeemKey::fromNonce($intent['nonce']),'fingerprint'=>RedeemFingerprint::make($owner,$host,$intent['selection']),'selection'=>$intent['selection'],'expires_at'=>$intent['exp']];} }
