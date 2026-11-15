<?php
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Booking\Public\PublicAvailabilityGateway;
use App\Booking\Public\PublicAvailabilityGuard;
use App\Booking\Public\PublicAvailabilityResponse;
use App\Booking\Public\PublicAvailabilityHeaders;
use App\Booking\Public\PublicAvailabilityError;
use Illuminate\Http\Request;
final class BookingPublicAvailabilityController extends Controller {
 public function index(Request $request,PublicAvailabilityGateway $gateway){
  try{
   $payload=$request->json()->all();if(!is_array($payload))throw new \InvalidArgumentException('Invalid JSON');
   PublicAvailabilityGuard::assertReadOnly($request->method(),$payload);
   $result=PublicAvailabilityResponse::guard($gateway->forHost($request->getHost(),$payload));
   return PublicAvailabilityHeaders::apply(response()->json($result));
  }catch(\Throwable $e){report($e);return PublicAvailabilityHeaders::apply(response()->json(['error'=>PublicAvailabilityError::message($e),'preview_only'=>true,'booking_writes_enabled'=>false],PublicAvailabilityError::status($e)));}
 }
}
