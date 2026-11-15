<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicAvailabilityResponse {
 public static function guard(array $response):array {
  if(($response['preview_only']??null)!==true||($response['booking_writes_enabled']??null)!==false||($response['appointment_created']??null)!==false)throw new \RuntimeException('Unsafe booking response');
  return ['available_starts'=>$response['available_starts'],'preview_only'=>true,'booking_writes_enabled'=>false,'appointment_created'=>false];
 }
}
