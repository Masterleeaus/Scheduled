<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemReceipt { public static function pending(string $key,string $fingerprint):array {return ['key'=>$key,'fingerprint'=>$fingerprint,'state'=>'claimed','confirmed'=>false,'payment_settled'=>false];} public static function committed(string $key,string $fingerprint,int $bookingId):array {if($bookingId<1)throw new \InvalidArgumentException('Invalid booking ID');return ['key'=>$key,'fingerprint'=>$fingerprint,'state'=>'committed','booking_id'=>$bookingId,'confirmed'=>true,'payment_settled'=>false];} }
