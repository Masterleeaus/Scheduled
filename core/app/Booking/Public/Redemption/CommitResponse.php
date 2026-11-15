<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitResponse { public static function confirmed(int $id,bool $replay=false):array {return ['state'=>'confirmed','booking_id'=>CommitBookingId::parse($id),'confirmed'=>true,'payment_settled'=>false,'replay'=>$replay];}}
