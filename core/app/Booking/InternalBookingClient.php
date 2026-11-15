<?php
namespace App\Booking;
use Illuminate\Support\Facades\Http;
/** Only signed internal sidecar commands; never expose arbitrary legacy routes. */
final class InternalBookingClient {
 private const ALLOWED=['services','booking_catalogue','health','busy_intervals','availability','booking_preview','booking_preview_capabilities','owner_booking_list','owner_booking_get','owner_booking_create','owner_booking_cancel','owner_booking_reschedule','owner_service_list','owner_service_create','owner_service_update'];
 public function call(array $tenant,string $operation,array $payload):array {
  if(!in_array($operation,self::ALLOWED,true))throw new \RuntimeException('Unsupported booking operation');
  $base=rtrim((string)config('booking_internal.endpoint',''),'/');$secret=(string)config('booking_internal.secret','');
  if(!preg_match('#^https://[a-z0-9.-]+(?::[0-9]{2,5})?$#i',$base)||strlen($secret)<32)throw new \RuntimeException('Private booking endpoint not configured');
  $body=json_encode(['operation'=>$operation,'payload'=>$payload],JSON_THROW_ON_ERROR);$now=time();$nonce=bin2hex(random_bytes(16));$key=$tenant['instance_key'];
  $signature=BookingEnvelopeSigner::sign($secret,$key,$body,$now,$nonce);
  $response=Http::timeout(15)->withHeaders(['Content-Type'=>'application/json','X-Booking-Instance'=>$key,'X-Booking-Time'=>(string)$now,'X-Booking-Nonce'=>$nonce,'X-Booking-Signature'=>$signature])->withBody($body,'application/json')->post($base.'/internal/bookings');
  if(!$response->successful())throw new \RuntimeException('Booking service request failed');
  $decoded=$response->json();if(!is_array($decoded))throw new \RuntimeException('Invalid booking response');return $decoded;
 }
}
