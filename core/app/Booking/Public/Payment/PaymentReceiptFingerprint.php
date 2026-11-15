<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Stable business fingerprint for comparing repeated provider events. */
final class PaymentReceiptFingerprint {
    public static function make(array $e): string {
        $required=['provider','event_id','payment_id','kind','booking_id','tenant_id','amount_minor','currency','payload_hash'];
        foreach($required as $key) if(!array_key_exists($key,$e))throw new \InvalidArgumentException('Missing payment receipt field');
        return hash('sha256',json_encode(array_values(array_map(static fn($key)=>$e[$key],$required)),JSON_THROW_ON_ERROR));
    }
    public static function replay(string $stored,string $incoming): bool {
        if(!preg_match('/^[a-f0-9]{64}$/D',$stored)||!preg_match('/^[a-f0-9]{64}$/D',$incoming))throw new \InvalidArgumentException('Invalid receipt fingerprint');
        if(!hash_equals($stored,$incoming))throw new \DomainException('Conflicting webhook event replay');
        return true;
    }
}
