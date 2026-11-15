<?php
namespace App\Booking\Public;
final class PublicBookingFlags {
 public static function guard(array $payload):void{if(($payload['preview_only']??null)!==true||($payload['booking_writes_enabled']??null)!==false)throw new \LogicException('Booking writes must remain disabled');}
}
