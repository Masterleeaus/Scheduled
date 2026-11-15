<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
final class CustomerBookingProjection {
    public static function fromPersisted(array $row,int $tenant,int $booking):array {
        $verified=CustomerBookingEvidence::requireRow($row,$tenant,$booking);
        return ['booking_reference'=>$verified['reference'],'status'=>$verified['status'],
            'confirmed'=>$verified['confirmed'],'can_manage'=>$verified['can_manage'],
            'payment_settled'=>false,'source'=>'persisted_booking',
            'message'=>$verified['confirmed']?'Booking confirmed':'Booking status: '.$verified['status']];
    }
}
