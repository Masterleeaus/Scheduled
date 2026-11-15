<?php
declare(strict_types=1);
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Booking\Public\PublicDomainRegistry;
use App\Booking\Public\PublicCatalogueGateway;
use App\Booking\Public\PublicAvailabilityGateway;
use App\Booking\Public\PublicAvailabilityResponse;
use App\Booking\Public\Intent\IntentInput;
use App\Booking\Public\Intent\IntentCatalogueProof;
use App\Booking\Public\Intent\IntentRequestProof;
use App\Booking\Public\Intent\IntentSlotProof;
use App\Booking\Public\Intent\IntentTimeProof;
use App\Booking\Public\Intent\IntentIssuer;
use App\Booking\Public\Intent\IntentResponse;
use App\Booking\Public\Intent\IntentSecret;
use Illuminate\Http\Request;

final class BookingPublicIntentController extends Controller {
    public function issue(Request $request, PublicDomainRegistry $domains,
                          PublicCatalogueGateway $catalogues, PublicAvailabilityGateway $availability) {
        try {
            $host = $request->getHost();
            $owner = $domains->ownerForHost($host);
            $input = $request->json()->all();
            if (!is_array($input) || count($input) > 16) throw new \InvalidArgumentException('Invalid booking intent');
            $selection = IntentInput::parse($input);
            IntentTimeProof::assertFuture($selection, time());
            IntentCatalogueProof::validate($selection, $catalogues->forHost($host));
            $slotInput = IntentRequestProof::availabilityInput($selection);
            $slots = PublicAvailabilityResponse::guard($availability->forHost($host, $slotInput));
            IntentSlotProof::validate($selection, $slots);
            $secret = IntentSecret::fromConfig(config('booking_internal.intent_secret'));
            $token = IntentIssuer::issue($host, $owner, $selection, $secret, time());
            return response()->json(IntentResponse::preview($token))
                ->header('Cache-Control', 'no-store, private')
                ->header('Pragma', 'no-cache')
                ->header('X-Content-Type-Options', 'nosniff');
        } catch (\Throwable $e) {
            report($e);
            // Do not expose tenant existence, catalogue identifiers or internal service failures.
            return response()->json([
                'error' => 'Booking intent unavailable',
                'preview_only' => true,
                'booking_writes_enabled' => false,
                'confirmed' => false,
            ], 422)->header('Cache-Control', 'no-store, private')
                ->header('X-Content-Type-Options', 'nosniff');
        }
    }
}
