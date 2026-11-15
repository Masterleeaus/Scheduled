<?php
declare(strict_types=1);
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Booking\Public\PublicDomainRegistry;
use App\Booking\Public\Checkout\CheckoutRequest;
use App\Booking\Public\Checkout\CheckoutNativeCustomer;
use App\Booking\Public\Checkout\CheckoutRecovery;
use App\Booking\Public\Checkout\CheckoutRecoveryReader;
use App\Booking\Public\Checkout\CheckoutTenantReadiness;
use App\Booking\Public\Checkout\CheckoutDeploymentProfile;
use App\Booking\Public\Checkout\CheckoutSubmissionExecutor;
use App\Booking\Public\Intent\IntentVerifier;
use App\Booking\Public\Intent\IntentSecret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/** A read-only, tenant-bound answer to a lost submit response. */
final class BookingPublicRecoveryController extends Controller {
 public function status(Request $request,PublicDomainRegistry $domains) {
  $headers=['Cache-Control'=>'private, no-store','Pragma'=>'no-cache','X-Content-Type-Options'=>'nosniff'];
  if(config('booking_internal.public_commit_enabled',false)!==true || !app()->bound(CheckoutSubmissionExecutor::class))
   return response()->json(['error'=>'Reservation status unavailable'],503,$headers);
  try {
   $raw=$request->json()->all();
   if(!is_array($raw))throw new \InvalidArgumentException('Invalid recovery request');
   $checkout=CheckoutRequest::parse($raw);
   $host=$request->getHost();$owner=$domains->ownerForHost($host);
   if(!CheckoutTenantReadiness::eligible(true,true,extension_loaded('pdo_mysql'),$owner,config('booking_internal.public_commit_tenants',[])))
    return response()->json(['error'=>'Reservation status unavailable'],503,$headers);
   $verified=IntentVerifier::verify($checkout['intent_token'],$host,$owner,
      IntentSecret::fromConfig(config('booking_internal.intent_secret')),time());
   $profiles=config('booking_internal.public_commit_tenants',[]);
   $profile=CheckoutDeploymentProfile::tenant($owner,$profiles);
   $pdo=DB::connection($profile['connection'])->getPdo();
   if(!($pdo instanceof \PDO))throw new \RuntimeException('Booking database unavailable');
   $result=CheckoutRecovery::lookup($owner,$verified['nonce'],$verified['selection'],$checkout['customer'],
      static fn($key,$tenant,$hash,$selection,$identity,$personalInfo)=>CheckoutRecoveryReader::read($pdo,$key,$tenant,$hash,$selection,$identity,$personalInfo));
   return response()->json($result,200,$headers);
  } catch(\InvalidArgumentException $e) {
   return response()->json(['error'=>'Invalid reservation details'],422,$headers);
  } catch(\Throwable $e) {
   report($e);
   return response()->json(['error'=>'Reservation status unavailable'],503,$headers);
  }
 }
}
