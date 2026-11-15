<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
final class CustomerBookingToken {
    public static function encode(array $payload, string $secret): string {
        CustomerBookingSecret::require($secret);
        $body=rtrim(strtr(base64_encode(json_encode($payload, JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
        if (strlen($body)>1024) throw new \InvalidArgumentException('Customer access payload oversized');
        return $body.'.'.hash_hmac('sha256', 'customer-booking-v1:'.$body, $secret);
    }
    public static function decode(string $token, string $secret): array {
        CustomerBookingSecret::require($secret);
        if (strlen($token)>1200 || !preg_match('/^([A-Za-z0-9_-]{1,1024})\.([a-f0-9]{64})$/D',$token,$m)) throw new \InvalidArgumentException('Invalid customer access');
        $expected=hash_hmac('sha256', 'customer-booking-v1:'.$m[1], $secret);
        if (!hash_equals($expected,$m[2])) throw new \InvalidArgumentException('Invalid customer access');
        $raw=base64_decode(strtr($m[1],'-_','+/'),true);
        if ($raw===false) throw new \InvalidArgumentException('Invalid customer access');
        $data=json_decode($raw,true,8,JSON_THROW_ON_ERROR);
        if (!is_array($data) || array_is_list($data)) throw new \InvalidArgumentException('Invalid customer access');
        return $data;
    }
}
