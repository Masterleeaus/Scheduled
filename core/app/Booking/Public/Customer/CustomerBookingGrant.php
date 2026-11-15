<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
final class CustomerBookingGrant {
    public static function issue(int $tenant, int $booking, string $secret, int $now): string {
        $tenant=CustomerBookingId::parse($tenant);$booking=CustomerBookingId::parse($booking);
        if ($now<1 || $now>2147480000) throw new \InvalidArgumentException('Invalid clock');
        return CustomerBookingToken::encode(['v'=>1,'scope'=>'booking_read','tenant'=>$tenant,'booking'=>$booking,'iat'=>$now,'exp'=>$now+900,'nonce'=>bin2hex(random_bytes(16))],$secret);
    }
    public static function verify(string $token, int $tenant, int $now, string $secret): int {
        $data=CustomerBookingToken::decode($token,$secret);
        if (count($data)!==7 || ($data['v']??null)!==1 || ($data['scope']??null)!=='booking_read' ||
            ($data['tenant']??null)!==CustomerBookingId::parse($tenant) ||
            !is_int($data['iat']??null) || !is_int($data['exp']??null) ||
            $data['iat'] > $now || $data['exp'] <= $now || $data['exp']-$data['iat']!==900 ||
            !is_string($data['nonce']??null) || !preg_match('/^[a-f0-9]{32}$/D',$data['nonce'])) {
            throw new \InvalidArgumentException('Invalid or expired customer access');
        }
        return CustomerBookingId::parse($data['booking']??null);
    }
}
