<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
final class CustomerBookingStatus {
    public static function interpret(mixed $value): array {
        if (!is_string($value)) throw new \DomainException('Booking status not verified');
        return match($value){
            'confirmed'=>['status'=>'confirmed','confirmed'=>true,'can_manage'=>true],
            'pending_payment'=>['status'=>'pending_payment','confirmed'=>false,'can_manage'=>false],
            'payment_processing'=>['status'=>'payment_processing','confirmed'=>false,'can_manage'=>false],
            'cancelled'=>['status'=>'cancelled','confirmed'=>false,'can_manage'=>false],
            'completed'=>['status'=>'completed','confirmed'=>false,'can_manage'=>false],
            'refunded'=>['status'=>'refunded','confirmed'=>false,'can_manage'=>false],
            default=>throw new \DomainException('Booking status not verified'),
        };
    }
}
