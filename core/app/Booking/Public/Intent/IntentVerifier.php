<?php
declare(strict_types=1);
namespace App\Booking\Public\Intent;
final class IntentVerifier {
    public static function verify(string $token,string $host,int $owner,string $secret,int $now):array {
        if(strlen($token)>4200 || substr_count($token,'.')!==1) throw new \InvalidArgumentException('Invalid booking intent');
        [$body,$mac]=explode('.',$token,2);
        IntentSigner::check($body,$mac,$secret);
        $data=IntentCodec::decode($body);
        if(($data['v']??null)!==1 || ($data['host']??null)!==IntentHost::require($host) ||
           ($data['owner']??null)!==$owner || ($data['scope']??null)!=='preview_only')
            throw new \InvalidArgumentException('Booking intent scope mismatch');
        if(!is_int($data['iat']??null) || !is_int($data['exp']??null) ||
           $data['iat']<=0 || $data['iat']>$now+30 || $data['exp']<=$data['iat'] ||
           $now>=$data['exp'] || $data['exp']-$data['iat']>900 ||
           !IntentNonce::valid($data['nonce']??null))
            throw new \InvalidArgumentException('Booking intent expired');
        $data['selection']=IntentInput::parse($data['selection']??[]);
        return $data;
    }
}
