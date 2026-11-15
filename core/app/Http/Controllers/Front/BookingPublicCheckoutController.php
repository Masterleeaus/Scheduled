<?php
declare(strict_types=1);
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Booking\Public\PublicDomainRegistry;
use App\Booking\Public\PublicCatalogueGateway;
use App\Booking\Public\PublicAvailabilityGateway;
use App\Booking\Public\PublicAvailabilityResponse;
use App\Booking\Public\Checkout\CheckoutRequest;
use App\Booking\Public\Checkout\CheckoutCommitCompatibility;
use App\Booking\Public\Intent\IntentVerifier;
use App\Booking\Public\Intent\IntentSecret;
use App\Booking\Public\Intent\IntentCatalogueProof;
use App\Booking\Public\Intent\IntentRequestProof;
use App\Booking\Public\Intent\IntentSlotProof;
use App\Booking\Public\Intent\IntentTimeProof;
use Illuminate\Http\Request;

/** Pre-commit checkout validation only. A separate authenticated release gate owns all booking writes. */
final class BookingPublicCheckoutController extends Controller {
    public function validateCheckout(Request $request,PublicDomainRegistry $domains,PublicCatalogueGateway $catalogues,PublicAvailabilityGateway $availability) {
        try {
            $data=$request->json()->all();
            if(!is_array($data)) throw new \InvalidArgumentException('Invalid request');
            $checkout=CheckoutRequest::parse($data);
            $host=$request->getHost();
            $owner=$domains->ownerForHost($host);
            $secret=IntentSecret::fromConfig(config('booking_internal.intent_secret'));
            $signed=IntentVerifier::verify($checkout['intent_token'],$host,$owner,$secret,time());
            $selection=$signed['selection'];
            IntentTimeProof::assertFuture($selection,time());
            IntentCatalogueProof::validate($selection,$catalogues->forHost($host));
            $slots=PublicAvailabilityResponse::guard($availability->forHost($host,IntentRequestProof::availabilityInput($selection)));
            IntentSlotProof::validate($selection,$slots);
            // A valid preview is not sufficient: check the existing native
            // writer's identity, column, and selection contracts too.
            CheckoutCommitCompatibility::assert($selection,$checkout['customer'],$signed['nonce']);
            // Do not return the signed capability, customer PII, or internal catalogue details.
            return response()->json(['checkout_valid'=>true,'confirmed'=>false,'payment_settled'=>false])
                ->header('Cache-Control','private, no-store')->header('Pragma','no-cache')->header('X-Content-Type-Options','nosniff');
        } catch(\InvalidArgumentException $e) {
            return response()->json(['error'=>'Invalid booking details','checkout_valid'=>false,'confirmed'=>false],422)
                ->header('Cache-Control','private, no-store')->header('Pragma','no-cache')->header('X-Content-Type-Options','nosniff');
        } catch(\Throwable $e) {
            report($e);
            return response()->json(['error'=>'Booking service unavailable','checkout_valid'=>false,'confirmed'=>false],503)
                ->header('Cache-Control','private, no-store')->header('Pragma','no-cache')->header('X-Content-Type-Options','nosniff');
        }
    }
}
