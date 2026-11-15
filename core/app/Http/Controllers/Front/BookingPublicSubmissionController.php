<?php
declare(strict_types=1);
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Booking\Public\PublicDomainRegistry;
use App\Booking\Public\PublicCatalogueGateway;
use App\Booking\Public\PublicAvailabilityGateway;
use App\Booking\Public\PublicAvailabilityResponse;
use App\Booking\Public\Checkout\CheckoutRequest;
use App\Booking\Public\Checkout\CheckoutSubmissionExecutor;
use App\Booking\Public\Checkout\CheckoutSubmissionGate;
use App\Booking\Public\Checkout\CheckoutTenantReadiness;
use App\Booking\Public\Intent\IntentVerifier;
use App\Booking\Public\Intent\IntentSecret;
use App\Booking\Public\Intent\IntentCatalogueProof;
use App\Booking\Public\Intent\IntentRequestProof;
use App\Booking\Public\Intent\IntentSlotProof;
use App\Booking\Public\Intent\IntentTimeProof;
use Illuminate\Http\Request;
/** Production reservation boundary: disabled unless a verified server-owned executor is registered. */
final class BookingPublicSubmissionController extends Controller {
 public function submit(Request $request, PublicDomainRegistry $domains, PublicCatalogueGateway $catalogues, PublicAvailabilityGateway $availability) {
  $headers=['Cache-Control'=>'private, no-store','Pragma'=>'no-cache','X-Content-Type-Options'=>'nosniff'];
  // Gate before reading customer data or revealing whether a hostname is a tenant.
  if (config('booking_internal.public_commit_enabled', false) !== true || !app()->bound(CheckoutSubmissionExecutor::class)) {
   return response()->json(['error'=>'Reservation service unavailable','confirmed'=>false],503,$headers);
  }
  try {
   $raw=$request->json()->all();
   if (!is_array($raw)) throw new \InvalidArgumentException('Invalid checkout request');
   $checkout=CheckoutRequest::parse($raw);
   $host=$request->getHost();$owner=$domains->ownerForHost($host);
   // Owner-specific readiness is checked again on submission: the global switch
   // is never sufficient to authorize another tenant's booking.
   if (!CheckoutTenantReadiness::eligible(
       true, app()->bound(CheckoutSubmissionExecutor::class), extension_loaded('pdo_mysql'),
       $owner, config('booking_internal.public_commit_tenants', [])
   )) {
       return response()->json(['error'=>'Reservation service unavailable','confirmed'=>false],503,$headers);
   }
   $secret=IntentSecret::fromConfig(config('booking_internal.intent_secret'));
   $verified=IntentVerifier::verify($checkout['intent_token'],$host,$owner,$secret,time());
   $selection=$verified['selection'];
   IntentTimeProof::assertFuture($selection,time());
   IntentCatalogueProof::validate($selection,$catalogues->forHost($host));
   $slots=PublicAvailabilityResponse::guard($availability->forHost($host,IntentRequestProof::availabilityInput($selection)));
   IntentSlotProof::validate($selection,$slots);
   // Customer-input problems are distinct from unavailable database/lock/persistence services.
  } catch (\InvalidArgumentException $e) {
   return response()->json(['error'=>'Invalid reservation details','confirmed'=>false],422,$headers);
  } catch (\Throwable $e) {
   report($e);
   return response()->json(['error'=>'Reservation service unavailable','confirmed'=>false],503,$headers);
  }
  try {
   // The executor must independently enforce tenant/price/availability under its DB locks.
   $result=CheckoutSubmissionGate::dispatch(true,app(CheckoutSubmissionExecutor::class),$owner,$selection,$checkout['customer'],$verified['nonce']);
   return response()->json($result, $result['replay'] === true ? 200 : 201, $headers);
  } catch (\Throwable $e) {
   report($e);
   return response()->json(['error'=>'Reservation service unavailable','confirmed'=>false],503,$headers);
  }
 }
}
