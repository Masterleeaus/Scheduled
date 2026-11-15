<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
final class StripeBookingWebhookGuard {
    public static function verify(string $raw,string $header,string $secret,int $now): array {
        StripeTestSignature::verify($raw,$header,$secret,$now);
        return StripeBookingEvent::parse($raw);
    }
    public static function enabled(array $config): void {
        if(($config['enabled']??false)!==true||($config['mode']??null)!=='test')throw new \DomainException('Test payment inbox disabled');
        $secret=$config['webhook_secret']??null;
        if(!is_string($secret)||!str_starts_with($secret,'whsec_')||strlen($secret)<16)throw new \DomainException('Webhook secret unavailable');
    }
}
